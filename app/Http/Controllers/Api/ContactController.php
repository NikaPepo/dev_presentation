<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTO\ContactDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function __construct(private readonly ContactService $contactService) {}

    /**
     * Submit a new contact-form message.
     *
     * Triggers an OpenAI analysis of the message (sentiment, category,
     * summary). If the AI service is unavailable, the contact is still saved
     * with `aiSummary` set to null — graceful degradation.
     *
     * Sends email notifications to the site owner and the submitter. Mail
     * failures are non-fatal and surfaced in `warnings[]`.
     *
     * Rate-limited: 5 requests / minute / IP by default (`throttle:contact`).
     *
     * @response 201 {
     *   "id": 42,
     *   "status": "success",
     *   "aiSummary": "Customer asking about pricing for the Pro plan.",
     *   "aiSentiment": "positive",
     *   "warnings": []
     * }
     */
    public function store(ContactRequest $request): JsonResponse
    {
        $dto = ContactDTO::fromRequest($request);
        $result = $this->contactService->submit($dto);

        $analysis = $result['analysis'];

        return response()->json([
            'id' => $result['contact']->id,
            'status' => 'success',
            'aiSummary' => $analysis?->summary,
            'aiSentiment' => $analysis?->sentiment->value,
            'warnings' => $result['warnings'],
        ], 201);
    }
}