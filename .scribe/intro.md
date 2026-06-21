# Introduction

Contact-form API with AI-assisted message analysis (OpenAI), email notifications, rate limiting, metrics and health checks.

<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>

    Public REST API for the contact form.

    All endpoints are JSON-only (`Accept: application/json`). Responses are auto-rendered as JSON for any `api/*` route thanks to `shouldRenderJsonWhen(fn ($req) => $req->is('api/*'))` in `bootstrap/app.php`.

    ### Error handling matrix

    | Status | When |
    |---|---|
    | **201 Created** | Contact submitted successfully. `aiSummary` may be `null` (graceful fallback if OpenAI is unavailable). `warnings[]` may contain messages about email delivery. |
    | **422 Unprocessable Entity** | Validation failed — see the `errors` object. |
    | **429 Too Many Requests** | Per-IP rate limit exceeded (`throttle:contact`, default 5/min). Includes `Retry-After` header. |
    | **500 Internal Server Error** | Database or unexpected server failure. |
    | **503 Service Unavailable** | `/api/health` only — the database is unreachable. |

