<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskPriority;

class TaskFilterRequest extends FormRequest
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
            'is_completed' => ['sometimes', 'boolean'],
            'priority' => ['sometimes', 'string', Rule::in([
                TaskPriority::LOW->value,
                TaskPriority::MEDIUM->value,
                TaskPriority::HIGH->value,
                TaskPriority::URGENT->value
            ])],
            'due_date' => ['sometimes', 'date', 'date_format:Y-m-d'],
            'sort_by' => ['sometimes', 'string', Rule::in([
                'created_at',
                'updated_at',
                'due_date',
                'priority',
                'is_completed'
            ])],
            'sort_order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
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
            'is_completed' => 'completion status',
            'priority' => 'task priority',
            'due_date' => 'due date',
            'sort_by' => 'sort field',
            'sort_order' => 'sort order',
            'per_page' => 'items per page',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_completed') && is_string($this->is_completed)) {
            $this->merge([
                'is_completed' => $this->is_completed === 'true'
            ]);
        }
    }
}