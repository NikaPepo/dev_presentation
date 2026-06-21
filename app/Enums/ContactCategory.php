<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactCategory: string
{
    case General = 'general';
    case Support = 'support';
    case Sales = 'sales';
    case Feedback = 'feedback';
    case BugReport = 'bug_report';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::General => 'General Inquiry',
            self::Support => 'Technical Support',
            self::Sales => 'Sales / Pre-sales',
            self::Feedback => 'Feedback',
            self::BugReport => 'Bug Report',
            self::Other => 'Other',
        };
    }

    /**
     * Map a free-form label returned by the AI into a known category.
     * Falls back to Other when the label is unrecognized.
     */
    public static function fromAiLabel(?string $label): self
    {
        if ($label === null) {
            return self::Other;
        }

        $normalized = strtolower(trim($label));

        return match (true) {
            str_contains($normalized, 'support'), str_contains($normalized, 'help') => self::Support,
            str_contains($normalized, 'sale'), str_contains($normalized, 'pricing') => self::Sales,
            str_contains($normalized, 'feedback'), str_contains($normalized, 'review') => self::Feedback,
            str_contains($normalized, 'bug'), str_contains($normalized, 'error'), str_contains($normalized, 'issue') => self::BugReport,
            str_contains($normalized, 'general'), str_contains($normalized, 'question') => self::General,
            default => self::Other,
        };
    }
}