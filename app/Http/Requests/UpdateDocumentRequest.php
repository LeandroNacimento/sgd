<?php

namespace App\Http\Requests;

use App\Enums\DocumentPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('document'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string', Rule::enum(DocumentPriority::class)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'document_state_id' => ['required', 'integer', 'exists:document_states,id'],
        ];
    }
}
