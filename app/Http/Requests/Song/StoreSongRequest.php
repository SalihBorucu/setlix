<?php

namespace App\Http\Requests\Song;

use App\Models\Band;
use Illuminate\Foundation\Http\FormRequest;

class StoreSongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $band = Band::findOrFail($this->band_id);
        return $band->isAdmin($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'band_id' => ['required', 'exists:bands,id'],
            'name' => ['required', 'string', 'max:255'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'url' => ['nullable', 'url', 'max:255'],
            'document' => [
                'nullable',
                'file',
                'mimes:pdf,txt',
                'max:10240', // 10MB max size
            ],
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
            'duration_seconds.required' => 'The song duration is required.',
            'duration_seconds.min' => 'The song duration must be at least 1 second.',
            'document.mimes' => 'The document must be a PDF or TXT file.',
            'document.max' => 'The document may not be larger than 10MB.',
        ];
    }
}
