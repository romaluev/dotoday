<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskPriority;

class TaskSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2'],
            'is_completed' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', 'string', Rule::in([
                TaskPriority::LOW->value,
                TaskPriority::MEDIUM->value,
                TaskPriority::HIGH->value,
                TaskPriority::URGENT->value
            ])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'query' => 'search query',
            'is_completed' => 'task completion status',
            'priority' => 'task priority',
            'per_page' => 'items per page',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => 'A search query is required',
            'query.min' => 'Search query must be at least 2 characters',
            'is_completed.boolean' => 'The task completion status must be true or false',
            'priority.in' => 'The priority must be one of: low, medium, high, urgent',
        ];
    }
}