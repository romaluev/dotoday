<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskPriority;

class TaskStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_completed' => ['nullable', 'boolean'],
            'due_date' => ['nullable', 'date', 'date_format:Y-m-d H:i:s'],
            'priority' => ['nullable', 'string', Rule::in([TaskPriority::LOW->value, TaskPriority::MEDIUM->value, TaskPriority::HIGH->value, TaskPriority::URGENT->value])],
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
            'title' => 'task title',
            'description' => 'task description',
            'is_completed' => 'task completion status',
            'due_date' => 'due date',
            'priority' => 'task priority',
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
            'title.required' => 'A task title is required',
            'is_completed.boolean' => 'The task completion status must be a boolean',
            'due_date.after' => 'The due date must be a future date and time',
            'priority.in' => 'The priority must be one of: low, medium, high, urgent',
        ];
    }
}