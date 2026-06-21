# Developer Presentation

A portfolio site / contact-form backend built with Laravel 13. Features a
fully working JSON API with AI-assisted message analysis (OpenAI), persistent
request logging, rate limiting, metrics, and a hand-crafted Blade + Alpine.js
front-end that consumes every endpoint live.

**Repository:** [github.com/NikaPepo/dev_presentation](https://github.com/NikaPepo/dev_presentation)

---

## Table of contents

1. [Quick start](#1-quick-start)
2. [Tech stack](#2-tech-stack)
3. [Architecture](#3-architecture)
4. [API reference](#4-api-reference)
5. [AI integration](#5-ai-integration)
6. [Built with AI assistance](#6-built-with-ai-assistance)
7. [Data storage & observability](#7-data-storage--observability)

---

## 1. Quick start

### Prerequisites

- **PHP 8.3+** with extensions: `pdo_mysql` or `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Node.js 20.19+ or 22.12+** (Vite 7 + Tailwind 4 require modern Node)
- **Composer 2.x**
- **Docker + Docker Compose** (optional but recommended — a full MySQL + Nginx + PHP stack is included)


```bash
# 1. Clone project
git clone https://github.com/NikaPepo/dev_presentation.git
cd dev_presentation

# 2. Environment setup
cp .env.example .env
# configure .env manually (DB, Mail, OpenAI)

# 3. Start Docker stack
docker compose up -d --build

# 4. Install PHP dependencies
docker compose exec app composer install

# 5. Generate application key
docker compose exec app php artisan key:generate

# 6. Run database migrations
docker compose exec app php artisan migrate --force

# 7. Install frontend dependencies (if applicable)
docker compose exec node npm install

# 8. Build frontend assets
docker compose exec node npm run build
```

---

## 2. Tech stack

### Backend

| Layer | Choice | Why |
|---|---|---|
| Language | PHP 8.3 | Typed properties, readonly classes, `match`, enums |
| Framework | Laravel 13.8 | Modern routing, queue, DI container, first-class testing |
| HTTP client | `Illuminate\Support\Facades\Http` | Built-in, retry support, no Guzzle dep needed |
| Migrations | Laravel Schema Blueprint | Native, no extra package |
| Validation | Laravel FormRequest + Validator | Built-in, expressive, integrates with Scribe |
| Mailing | Laravel Mail (SMTP) | Driver-agnostic, swap via `MAIL_MAILER` |
| API docs | `knuckleswtf/scribe ^5.11` | Auto-derives docs from FormRequest + `@response` annotations |
| Logging | Monolog (Laravel default) + custom `api-requests` channel | One channel per concern, easy to swap |

### Front-end

| Layer | Choice | Why |
|---|---|---|
| Templating | Laravel Blade | Server-rendered, SEO-friendly, no JS build required for content |
| Interactivity | Alpine.js 3.15 | ~15 KB, no virtual DOM, lives inside HTML attributes |
| Styling | Tailwind CSS 4 | Utility-first, no custom CSS to maintain |
| Build | Vite 7 | Fast HMR, first-class Laravel plugin |

### AI

| Tool | Used for |
|---|---|
| OpenAI Chat Completions (`gpt-4o-mini` by default) | Sentiment + category + summary + suggested-reply analysis of contact-form messages |

### Infrastructure

| Component | Notes |
|---|---|
| Docker Compose | `docker-compose.yml` with `app` (PHP-FPM), `nginx`, `mysql:8.0` |
| MySQL 8.0 | Primary DB for `contacts`, `metrics`, `api_request_logs`, plus Laravel's `users/cache/jobs` tables |
| Nginx | Serves `public/` and forwards PHP requests to the app container |

---

## 3. Architecture

### Directory layout (only the bits that matter)

```
app/
├── DTO/                     # Readonly data carriers between HTTP and service layers
│   ├── ContactDTO.php
│   └── AIAnalysisDTO.php
│
├── Enums/
│   └── SentimentType.php    # Positive | Neutral | Negative
│
├── Exceptions/              # Domain-specific exceptions
│   ├── AIServiceException.php
│   ├── RateLimitException.php        (reserved; not used in the default path)
│   └── MailSendingException.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/             # JSON API endpoints (used by the front-end and external clients)
│   │   │   ├── ContactController.php   (POST /api/contact)
│   │   │   ├── AnalyzeController.php   (POST /api/analyze)
│   │   │   ├── HealthController.php    (GET  /api/health)
│   │   │   └── MetricsController.php   (GET  /api/metrics, /api/metrics/summary)
│   │   └── Web/             # Blade-rendered pages
│   │       ├── LandingController.php
│   │       ├── ApiDemoController.php
│   │       ├── HealthPageController.php
│   │       └── MetricsPageController.php
│   │
│   ├── Requests/            # FormRequest classes — single source of truth for validation
│   │   └── ContactRequest.php
│   │
│   └── Middleware/
│       └── ApiRequestLogger.php   # Logs every API request to file + DB
│
├── Mail/
│   ├── OwnerContactMail.php
│   └── UserContactMail.php
│
├── Models/                  # Eloquent
│   ├── Contact.php
│   ├── Metric.php
│   └── ApiRequestLog.php
│
├── Repositories/
│   ├── Contracts/           # Interfaces
│   │   ├── ContactRepositoryInterface.php
│   │   └── MetricRepositoryInterface.php
│   └── *.php                # Eloquent implementations
│
├── Services/                # Business logic — controllers stay thin
│   ├── ContactService.php   # Orchestrates AI → save → mail → metrics
│   ├── AIService.php
│   ├── MailService.php
│   ├── MetricsService.php
│   └── HealthService.php
│
├── Handlers/
│   └── OpenAIHandler.php    # Low-level HTTP client for OpenAI
│
└── Providers/
    └── AppServiceProvider.php   # Binds interfaces, registers RateLimiter
```

### Design patterns

- **DTO ↔ FormRequest boundary.** Controllers convert FormRequest → DTO via `ContactDTO::fromRequest()`. Services receive a `readonly` DTO and never touch the HTTP layer.
- **Repository pattern.** `ContactRepositoryInterface` / `MetricRepositoryInterface` are bound to Eloquent implementations in `AppServiceProvider`. Swapping the implementation (e.g. for tests or a future read-replica) is one line.
- **Graceful degradation in `ContactService`.** The orchestration is wrapped in try/catch around each external dependency:
  - AI call fails → contact still saves, `ai_*` fields stay `NULL`, response returns 201.
  - Mail fails → contact still saves, failure surfaces in `warnings[]`, response returns 201.
  - DB save fails → exception propagates, response returns 500.
- **Middleware-aliased logging.** `ApiRequestLogger` is bound to `'api.logger'` in `bootstrap/app.php` and runs `terminate()` after every API response. Every request is recorded twice — once to a file (`storage/logs/api-requests.log`) and once to the `api_request_logs` table.
- **Built-in rate limiting.** `RateLimiter::for('contact', …)` is registered in `AppServiceProvider::boot()`. The route declares `middleware('throttle:contact')`. No custom middleware needed.
- **Stateless AI endpoint.** `POST /api/analyze` is a separate route that does not require a contact form. It exists for the live demo on the landing page — same `AIService::analyze()` call but no DB write and no email.

### Why this stack and not [Vue / Nuxt / Livewire / Inertia]?

- **Vue / React + Vite as SPA** would duplicate routing (web routes + client-side routes), require a separate auth/state layer, and pull in a 50 KB+ runtime for an app that mostly renders server-side. Overkill.
- **Livewire 3** is a strong choice for reactive forms, but adds a different mental model (server-driven reactivity). Fine for someone familiar with it.
- **Inertia.js** would require controllers that return either JSON or Inertia responses depending on the request — additional branching for marginal benefit on a project this size.
- **Blade + Alpine + Tailwind** gives 90% of the interactivity with 10% of the complexity. Alpine lives inside HTML (`x-data`, `x-show`), so the front-end stays readable as a Blade template with sprinkles of JS.

---

## 4. API reference

Live, interactive docs (with `Try It Out` and curl/JS/PHP examples) are auto-generated and available at **`/docs`** thanks to Scribe. The summary:

### Endpoints

| Method | Path | Purpose | Auth | Rate-limited |
|---|---|---|---|---|
| `POST` | `/api/contact` | Submit a contact form (writes to DB, sends emails, AI-analyzes) | none | 5/min/IP |
| `POST` | `/api/analyze` | Stateless AI analysis of a message | none | no |
| `GET`  | `/api/health` | Component health (db / cache / ai) | none | no |
| `GET`  | `/api/metrics` | Most recent 200 raw metric events | none | no |
| `GET`  | `/api/metrics/summary` | Aggregated count/sum/avg/min/max over the last 24 h | none | no |

### Examples

**Submit a contact form:**

```bash
curl -X POST http://localhost:8080/api/contact \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "phone": "+1 555 123 4567",
    "message": "I would like to discuss a partnership opportunity."
  }'
```

```json
// 201 Created
{
  "id": 42,
  "status": "success",
  "aiSummary": "Customer asking about partnership.",
  "aiSentiment": "positive",
  "warnings": []
}
```

**AI analysis (no DB write, no email):**

```bash
curl -X POST http://localhost:8080/api/analyze \
  -H 'Content-Type: application/json' \
  -d '{"message": "I have been waiting for a refund for three weeks. This is unacceptable."}'
```

```json
// 200 OK
{
  "sentiment": "negative",
  "category": "support",
  "confidence": 0.92,
  "summary": "Customer complaint about a delayed refund.",
  "suggestedReply": "Apologise and offer to escalate to a manager."
}
```

**Health:**

```bash
curl http://localhost:8080/api/health
```

```json
// 200 OK when all three components are healthy
{ "status": "ok",       "components": { "db": "ok", "cache": "ok", "ai": "ok" } }
// 200 OK when only the DB is up (graceful — app still works)
{ "status": "degraded", "components": { "db": "ok", "cache": "ok", "ai": "fail" } }
// 503 Service Unavailable when the DB is down
{ "status": "fail",     "components": { "db": "fail", "cache": "ok", "ai": "ok" } }
```

### Validation and error handling

| Status | When | Body |
|---|---|---|
| **201** | Contact saved. `aiSummary` may be `null`; `warnings[]` may contain mail-failure notices. | `{id, status, aiSummary, aiSentiment, warnings}` |
| **422** | Validation failed | `{message, errors: {field: […]}}` |
| **429** | Per-IP rate limit hit (5/min default) | `{message: "Too Many Requests."}` + `Retry-After` header |
| **500** | DB unreachable or unhandled server error | `{message}` (Laravel default JSON renderer) |
| **502** | `/api/analyze` only — OpenAI returned a non-2xx response | `{error: "AI service unavailable", detail}` |

Validation rules live in `ContactRequest::rules()`:

```php
'name'    => ['required', 'string', 'min:2', 'max:120'],
'email'   => ['required', 'string', 'email:rfc,dns', 'max:255'],
'phone'   => ['required', 'string', 'max:30'],
'message' => ['required', 'string', 'min:10', 'max:5000'],
```

The `email:rfc,dns` rule performs a live DNS lookup, so it requires the server to have working outbound DNS. If you deploy behind a restrictive network, switch to `email:rfc` to skip the lookup.

---

## 5. AI integration

### Provider

- **OpenAI Chat Completions**, default model `gpt-4o-mini`
- Endpoint: `POST {base_url}/chat/completions` (defaults to `https://api.openai.com/v1`)
- Auth: `Authorization: Bearer {OPENAI_API_KEY}`

### Prompt (system message)

```
You analyze contact-form messages. Reply with JSON only, no markdown.
Schema: { "sentiment": "positive"|"neutral"|"negative",
          "category": "general"|"support"|"sales"|"feedback"|"other",
          "confidence": 0..1,
          "summary": "<= 25 words",
          "suggested_reply": "<= 30 words or null" }
```

The model is instructed to use `response_format: json_object` to guarantee parseable output.

### Retry and timeouts

`OpenAIHandler::complete()` uses `Http::retry(3, 200, throw: false)` — three attempts with a 200 ms backoff. Total worst-case wait ≈ 15 s (`OPENAI_TIMEOUT=15`). HTTP 4xx errors are not retried (they indicate a bad request, not a transient failure).

### Fallback strategy

The AI call sits **inside** a try/catch in `ContactService::submit()`. On any failure:

```php
try {
    $analysis = $this->ai->analyze($dto->message);
    $this->metrics->recordAiCall(true, /* duration_ms */);
} catch (AIServiceException $e) {
    report($e);
    $this->metrics->recordAiCall(false, /* duration_ms */);
    $analysis = null;
}
```

The contact is still created, the mail still goes out, the response is still **201 Created**. `ai_summary`, `ai_sentiment`, `ai_confidence` columns stay `NULL`. The front-end shows `aiSummary: null` in the success card.

### Cost notes

- `gpt-4o-mini` is ~$0.15/M input tokens. A typical 30-word contact message plus the system prompt is under 200 tokens total per request → fractions of a cent per call.
- For local development without a key, set `OPENAI_API_KEY=` (empty) — `OpenAIHandler` throws immediately and the fallback path runs on every request.

---

## 6. Built with AI assistance

This project was built collaboratively with **Claude (Anthropic)** acting as a pair-programmer. Honest disclosure of what was generated vs. what was touched by hand:

### What the AI generated end-to-end

- The initial DTO / Enum / Exception / Repository / Service class skeletons (boilerplate that just needed renaming)
- The full Blade templates for the landing page, AI demo, contact form, API playground, health and metrics pages
- The Alpine.js front-end logic (`x-data` reactive blocks, fetch wrappers, error display)
- `OpenAIHandler` retry / timeout logic
- The Markdown email templates
- The Scribe `bodyParameters()` metadata
- The README you are reading now

### What was corrected by hand

| Symptom | Root cause | Fix |
|---|---|---|
| All API requests returned **500** with `Target class [ApiRequestLogger] does not exist` | `bootstrap/app.php` referenced `ApiRequestLogger::class` without `use App\Http\Middleware\ApiRequestLogger;` — the alias was registered as the bare string `"ApiRequestLogger"` | Added the `use` import |
| Form threw `The email field must be a valid email address` on the AI demo | The demo posted to `/api/contact` with a hardcoded `demo@example.com` and the `email:rfc,dns` rule failed the DNS lookup | Split out a separate `POST /api/analyze` endpoint that takes only `{message}` |
| `Call to a member function label() on null` in `owner-contact.blade.php` | Removed `category` from the DB but forgot to update the email template that called `$contact->category->label()` | Removed the `**Category:**` line from the email template |
| OpenAI API key was leaked in chat history | (Pasting real keys into a chat transcript) | Revoked and rotated. **API keys, App Passwords, and any other secrets never belong in chat logs.** |
| `npm run dev` failed with "Cannot find native binding" on Linux x64 | `rolldown` (Vite 8's bundler) needs Node ≥ 20.19 + platform-specific optional deps that were filtered out | Downgraded to Vite 7 + `laravel-vite-plugin` v2 (no Rolldown) |

### What would still change

- The landing page hero is text-only. A small SVG/avatar would make it more visually anchored.
- There is no automated test suite — only the stock Laravel `ExampleTest.php` files. A small `tests/Feature/ContactApiTest.php` with happy-path + 422 + 429 + 502 cases would catch regressions cheaply.
- `ApiRequestLogger` writes synchronously in `terminate()`. For a high-traffic site this should be queued.

---

## 7. Data storage & observability

### MySQL tables created by the project

```
contacts           — one row per submitted contact form
  id, name, email, phone, message,
  ai_sentiment, ai_summary, ai_confidence,    -- nullable; populated if OpenAI was up
  ip, user_agent,                            -- nullable
  created_at, updated_at

metrics            — application-level counters and gauges
  id, name, type ('counter'|'gauge'), value (decimal), tags (json), occurred_at, ...

api_request_logs   — one row per API request
  id, method, path, status, ip, user_agent,
  duration_ms, request_id (uuid), metadata (json), occurred_at, ...
```

Plus the default Laravel tables: `users`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `migrations`.

### How request logs are stored

Every API request goes through the `api.logger` middleware (bound in `bootstrap/app.php`). The middleware:

1. In `handle()`: stamps `start_time` and a `request_id` (UUID) onto the `Request` attributes.
2. In `terminate()`: computes `duration_ms` and writes the record in **two** places:
   - **File**: `Log::channel('api-requests')->info('api.request', $payload)` → `storage/logs/api-requests.log`. The `api-requests` channel is configured in `config/logging.php` as a single-file driver with a 14-day rotation.
   - **Database**: `ApiRequestLog::create($payload)` → `api_request_logs` table.
3. Both writes are wrapped in `try { ... } catch (Throwable) { /* swallow */ }` so a logging failure never breaks the response.

### How rate limiting is stored

The default `RateLimiter::for('contact', …)` registers in `AppServiceProvider::boot()`:

```php
RateLimiter::for('contact', function (Request $request) {
    return Limit::perMinute((int) config('services.rate_limit.per_minute', 5))
        ->by($request->ip());
});
```

The limiter uses Laravel's built-in `Cache` repository. With `CACHE_STORE=database` (the `.env` default), counters are stored in the `cache` table. The route declares `middleware('throttle:contact')` and Laravel returns **429** with a `Retry-After` header automatically — no custom middleware needed.

### Where statistics live

Application metrics go to the `metrics` table via `MetricRepository`:

- `recordContact()` → `counter('contacts.total')`, optional `gauge('contacts.ai_duration_ms')`
- `recordAiCall($ok, $ms)` → `counter('ai.success' | 'ai.failure')`, `gauge('ai.duration_ms', …)`
- `recordMailFailure()` → `counter('mail.failures')`

The summary endpoint (`GET /api/metrics/summary`) aggregates these into `{count, sum, avg, min, max}` over the last 24 hours via `MetricRepository::summary()` and caches the result in the default cache for 60 seconds.

### Viewing the data

- **Live metrics UI**: `/metrics` — shows recent events and 24 h summary, refreshes on reload.
- **Health UI**: `/health` — colour-coded status with auto-refresh every 15 s.
- **API playground**: `/api-demo` — interactive buttons that fire each endpoint and pretty-print the JSON response.

