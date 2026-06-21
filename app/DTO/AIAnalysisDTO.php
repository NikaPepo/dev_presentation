<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enums\ContactCategory;
use App\Enums\SentimentType;

/**
 * Result of an AI analysis call. Used both internally by AIService and as
 * the shape stored on the Contact model (category, sentiment, summary...).
 */
final readonly class AIAnalysisDTO
{
    public function __construct(
        public SentimentType $sentiment,
        public ContactCategory $category,
        public float $confidence,
        public string $summary,
        public ?string $suggestedReply = null,
    ) {}

    /**
     * Build an analysis DTO from a parsed OpenAI chat-completions response.
     * Tolerates a few field-name variants the model might return.
     *
     * @param  array<string, mixed>  $response  the assistant message payload
     */
    public static function fromOpenAiResponse(array $response): self
    {
        $sentiment = SentimentType::fromAiLabel(
            (string) ($response['sentiment'] ?? $response['tone'] ?? '')
        );

        $category = ContactCategory::fromAiLabel(
            (string) ($response['category'] ?? $response['topic'] ?? '')
        );

        $confidence = isset($response['confidence'])
            ? max(0.0, min(1.0, (float) $response['confidence']))
            : 0.5;

        $summary = (string) ($response['summary'] ?? $response['short_summary'] ?? '');

        $suggested = $response['suggested_reply']
            ?? $response['reply']
            ?? null;

        return new self(
            sentiment: $sentiment,
            category: $category,
            confidence: $confidence,
            summary: $summary,
            suggestedReply: is_string($suggested) && $suggested !== '' ? $suggested : null,
        );
    }
}