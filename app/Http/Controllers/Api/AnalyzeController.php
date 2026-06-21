<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Exceptions\AIServiceException;
use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Stateless AI analyzer — used by the "Try the AI analyzer" demo on the
 * landing page. Unlike POST /api/contact, it does NOT save anything to the
 * database and does NOT require name/email/phone. It just runs sentiment +
 * category + summary on a free-form message and returns the result.
 *
 * @response 200 {
 *   "sentiment": "positive",
 *   "category": "sales",
 *   "confidence": 0.9,
 *   "summary": "Customer asking about pricing."
 * }
 * @response 422 {"message": "...", "errors": {"message": ["..."]}}
 * @response 502 {"error": "AI service unavailable"}
 */
class AnalyzeController extends Controller
{
    public function __construct(private readonly AIService $ai) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = Validator::make($request->all(), [
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ])->validate();

        try {
            $analysis = $this->ai->analyze($data['message']);

            return response()->json([
                'sentiment' => $analysis->sentiment->value,
                'category' => $analysis->category->value,
                'confidence' => $analysis->confidence,
                'summary' => $analysis->summary,
                'suggestedReply' => $analysis->suggestedReply,
            ]);
        } catch (AIServiceException $e) {
            report($e);
            return response()->json([
                'error' => 'AI service unavailable',
                'detail' => $e->getMessage(),
            ], 502);
        }
    }
}