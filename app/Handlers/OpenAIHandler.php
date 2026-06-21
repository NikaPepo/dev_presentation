<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Exceptions\AIServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Thin wrapper around the OpenAI Chat Completions API.
 *
 * Kept low-level on purpose — AIService handles parsing, retry policy and
 * graceful degradation. This class only speaks HTTP and returns the raw
 * assistant message payload (or throws AIServiceException on transport
 * failures / non-2xx responses).
 */
class OpenAIHandler
{
    public function complete(array $messages, ?string $model = null, ?float $timeout = null): array
    {
        $apiKey = (string) config('services.openai.api_key');
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $resolvedModel = $model ?: (string) config('services.openai.model', 'gpt-4o-mini');
        $resolvedTimeout = $timeout ?: (float) config('services.openai.timeout', 15);

        if ($apiKey === '') {
            throw new AIServiceException(
                message: 'OpenAI API key is not configured',
                provider: 'openai',
                httpStatus: null,
                originalMessage: 'Set OPENAI_API_KEY in .env',
            );
        }

        try {
            $response = Http::baseUrl($baseUrl)
                ->withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout($resolvedTimeout)
                ->retry(3, 200, throw: false)
                ->post('/chat/completions', [
                    'model' => $resolvedModel,
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (! $response->successful()) {
                throw new AIServiceException(
                    message: 'OpenAI returned a non-2xx response',
                    provider: 'openai',
                    httpStatus: $response->status(),
                    originalMessage: $response->body(),
                );
            }

            $content = data_get($response->json(), 'choices.0.message.content');

            if (! is_string($content) || $content === '') {
                throw new AIServiceException(
                    message: 'OpenAI response is missing message content',
                    provider: 'openai',
                    httpStatus: $response->status(),
                    originalMessage: $response->body(),
                );
            }

            $decoded = json_decode($content, true);

            if (! is_array($decoded)) {
                throw new AIServiceException(
                    message: 'OpenAI response content is not valid JSON',
                    provider: 'openai',
                    httpStatus: $response->status(),
                    originalMessage: $content,
                );
            }

            return $decoded;
        } catch (ConnectionException $e) {
            throw new AIServiceException(
                message: 'Could not connect to OpenAI',
                provider: 'openai',
                httpStatus: null,
                originalMessage: $e->getMessage(),
                previous: $e,
            );
        } catch (RequestException $e) {
            throw new AIServiceException(
                message: 'OpenAI HTTP error',
                provider: 'openai',
                httpStatus: $e->response?->status(),
                originalMessage: $e->getMessage(),
                previous: $e,
            );
        } catch (AIServiceException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new AIServiceException(
                message: 'Unexpected error talking to OpenAI',
                provider: 'openai',
                httpStatus: null,
                originalMessage: $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * Lightweight reachability check used by HealthService. Returns true if
     * the API answers without an auth error.
     */
    public function ping(): bool
    {
        $apiKey = (string) config('services.openai.api_key');
        if ($apiKey === '') {
            return false;
        }

        try {
            $response = Http::baseUrl(rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/'))
                ->withToken($apiKey)
                ->timeout(5)
                ->get('/models');

            return $response->status() !== 401 && $response->status() !== 403;
        } catch (Throwable) {
            return false;
        }
    }
}