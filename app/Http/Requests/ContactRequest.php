<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ContactCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the contact-form payload.
     *
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\In>>
     */
    public function rules(): array
    {
        return [
            /** @example "Jane Doe" */
            'name' => ['required', 'string', 'min:2', 'max:120'],

            /** @example "jane@example.com" */
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],

            /** @example "+79991234567" */
            'phone' => ['required', 'string', 'max:30'],

            /** @example "I'd like to ask about enterprise pricing for the Pro plan." */
            'message' => ['required', 'string', 'min:10', 'max:5000'],

            /** @example "sales" */
            'category' => ['required', 'string', Rule::in(array_column(ContactCategory::cases(), 'value'))],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'category.in' => 'Category must be one of: '.implode(', ', array_column(ContactCategory::cases(), 'value')),
        ];
    }

    /**
     * Body parameters metadata for Scribe API documentation.
     * Each entry: ['type' => ..., 'description' => ..., 'required' => bool].
     *
     * @return array<string, array{type:string,description:string,required:bool}>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'type' => 'string',
                'description' => 'Submitter full name (2–120 characters).',
                'required' => true,
            ],
            'email' => [
                'type' => 'string',
                'description' => 'Submitter email (validated with RFC + DNS lookup).',
                'required' => true,
            ],
            'phone' => [
                'type' => 'string',
                'description' => 'Submitter phone number (max 30 chars, free format).',
                'required' => true,
            ],
            'message' => [
                'type' => 'string',
                'description' => 'Free-form message text (10–5000 characters).',
                'required' => true,
            ],
            'category' => [
                'type' => 'string',
                'description' => 'One of: '.implode(', ', array_column(ContactCategory::cases(), 'value')),
                'required' => true,
            ],
        ];
    }
}