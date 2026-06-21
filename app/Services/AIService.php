<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\AIAnalysisDTO;
use App\Exceptions\AIServiceException;
use App\Handlers\OpenAIHandler;

class AIService
{
    public function __construct(private readonly OpenAIHandler $openAi) {}

    /**
     * Run sentiment + category + summary analysis on a free-form message.
     * Throws AIServiceException on any failure — callers should catch and
     * fall back gracefully (never surface a 5xx to the end user).
     */
    public function analyze(string $message): AIAnalysisDTO
    {
        $payload = $this->openAi->complete([
            [
                'role' => 'system',
                'content' => 'You analyze contact-form messages. Reply with JSON only, no markdown. '
                    .'Schema: { "sentiment": "positive"|"neutral"|"negative", '
                    .'"category": "general"|"support"|"sales"|"feedback"|"bug_report"|"other", '
                    .'"confidence": 0..1, "summary": "<= 25 words", "suggested_reply": "<= 30 words or null" }',
            ],
            [
                'role' => 'user',
                'content' => $message,
            ],
        ]);

        return AIAnalysisDTO::fromOpenAiResponse($payload);
    }

    public function ping(): bool
    {
        return $this->openAi->ping();
    }
}