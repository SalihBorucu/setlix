<?php

namespace App\Http\Requests\Setlist;

use App\Models\Band;
use Illuminate\Foundation\Http\FormRequest;

class StoreSetlistRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:1000'],
            'song_order' => ['nullable', 'array'],
            'song_order.*' => ['exists:songs,id'],
            'total_duration' => ['nullable', 'integer', 'min:0'],
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
            'song_order.*.exists' => 'One or more selected songs do not exist.',
            'total_duration.min' => 'The total duration cannot be negative.',
        ];
    }
}
