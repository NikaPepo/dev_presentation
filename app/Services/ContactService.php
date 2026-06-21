<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\AIAnalysisDTO;
use App\DTO\ContactDTO;
use App\Exceptions\AIServiceException;
use App\Models\Contact;
use App\Repositories\Contracts\ContactRepositoryInterface;
use Throwable;

/**
 * Orchestrates the contact-form submission pipeline:
 *   1. AI analysis  (graceful: never aborts the request)
 *   2. Persist contact (failure here IS fatal — bubbles up to 500)
 *   3. Send mail    (graceful: failure → warnings[])
 *   4. Record metric (best-effort)
 *
 * Returns a structured result consumed by ContactController.
 */
class ContactService
{
    public function __construct(
        private readonly ContactRepositoryInterface $contacts,
        private readonly AIService $ai,
        private readonly MailService $mail,
        private readonly MetricsService $metrics,
    ) {}

    /**
     * @return array{contact: Contact, analysis: ?AIAnalysisDTO, warnings: string[]}
     */
    public function submit(ContactDTO $dto): array
    {
        // 1) AI analysis — graceful, never throws further
        $analysis = null;
        $aiStart = microtime(true);

        try {
            $analysis = $this->ai->analyze($dto->message);
            $this->metrics->recordAiCall(true, (microtime(true) - $aiStart) * 1000);
        } catch (AIServiceException $e) {
            report($e);
            $this->metrics->recordAiCall(false, (microtime(true) - $aiStart) * 1000);
        }

        // 2) Persist contact — failure here is fatal (500)
        $contact = $this->contacts->create($dto);

        if ($analysis !== null) {
            $contact->forceFill([
                'ai_sentiment' => $analysis->sentiment->value,
                'ai_summary' => $analysis->summary,
                'ai_confidence' => $analysis->confidence,
            ])->save();
        }

        // 3) Send mail — graceful, returns warnings
        $warnings = [];
        try {
            $warnings = $this->mail->sendOwnerAndUser($contact);
        } catch (Throwable $e) {
            report($e);
            $warnings[] = 'Email notifications could not be sent';
            $this->metrics->recordMailFailure();
        }

        // 4) Metric — best-effort
        try {
            $this->metrics->recordContact(
                $contact->category,
                $analysis !== null ? (microtime(true) - $aiStart) * 1000 : null,
            );
        } catch (Throwable $e) {
            report($e);
        }

        return [
            'contact' => $contact,
            'analysis' => $analysis,
            'warnings' => $warnings,
        ];
    }
}