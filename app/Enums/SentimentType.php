<?php

declare(strict_types=1);

namespace App\Enums;

enum SentimentType: string
{
    case Positive = 'positive';
    case Neutral = 'neutral';
    case Negative = 'negative';

    public function label(): string
    {
        return match ($this) {
            self::Positive => 'Positive',
            self::Neutral => 'Neutral',
            self::Negative => 'Negative',
        };
    }

    /**
     * Numeric thresholds (based on OpenAI-style sentiment scores in [-1, 1]):
     *   >=  0.2  → Positive
     *   <= -0.2  → Negative
     *   else     → Neutral
     */
    public static function fromScore(float $score): self
    {
        return match (true) {
            $score >= 0.2 => self::Positive,
            $score <= -0.2 => self::Negative,
            default => self::Neutral,
        };
    }

    public static function fromAiLabel(?string $label): self
    {
        return match (strtolower(trim((string) $label))) {
            'positive', 'pos', 'good', 'happy' => self::Positive,
            'negative', 'neg', 'bad', 'angry' => self::Negative,
            default => self::Neutral,
        };
    }
}