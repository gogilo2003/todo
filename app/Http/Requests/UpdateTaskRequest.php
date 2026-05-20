<?php

namespace App\Http\Requests;

use App\Constants\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Request validation for updating a task.
 */
class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the TaskPolicy
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
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'project_id' => ['sometimes', 'required', 'integer', 'exists:projects,id'],
            'priority' => ['sometimes', 'required', 'string', 'in:' . implode(',', TaskPriority::values())],
            'due_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'priority.in' => 'The selected priority must be one of: ' . implode(', ', TaskPriority::values()),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('due_date') && $this->due_date === '') {
            $this->merge(['due_date' => null]);
        }
    }
}
