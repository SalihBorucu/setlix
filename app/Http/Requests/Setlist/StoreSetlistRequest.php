<?php

namespace App\Http\Requests\Setlist;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Band;

class StoreSetlistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the band from the form data since we're creating a new setlist
        $band = Band::findOrFail($this->band_id);
        return $this->user()->can('update', $band);
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
            'description' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'in:song,break'],
            // For songs
            'items.*.song_id' => ['nullable', 'required_if:items.*.type,song', 'exists:songs,id'],
            // For breaks
            'items.*.title' => ['nullable', 'required_if:items.*.type,break', 'string', 'max:255'],
            // Common fields
            'items.*.duration_seconds' => ['required', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
            'total_duration' => ['required', 'integer', 'min:1']
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
            'items.*.song_id.required_if' => 'A song ID is required for song items.',
            'items.*.title.required_if' => 'A title is required for break items.',
            'items.*.song_id.exists' => 'One or more selected songs do not exist.',
            'items.*.duration_seconds.min' => 'Duration must be at least 1 second.',
            'total_duration.min' => 'The total duration cannot be negative.',
        ];
    }
}
