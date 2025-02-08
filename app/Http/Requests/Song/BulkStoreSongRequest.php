<?php

namespace App\Http\Requests\Song;

use App\Models\Band;
use Illuminate\Foundation\Http\FormRequest;

class BulkStoreSongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $band = Band::findOrFail($this->route('band')->id);
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
            'songs' => ['required', 'array', 'min:1', 'max:30'],
            'songs.*.name' => ['required', 'string', 'max:255'],
            'songs.*.duration_seconds' => ['required', 'integer', 'min:1', 'max:7200'], // max 2 hours
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
            'songs.required' => 'At least one song is required.',
            'songs.max' => 'You can only add up to 30 songs at once.',
            'songs.*.name.required' => 'Each song must have a name.',
            'songs.*.name.max' => 'Song names cannot be longer than 255 characters.',
            'songs.*.duration_seconds.required' => 'Each song must have a duration.',
            'songs.*.duration_seconds.min' => 'Song duration must be at least 1 second.',
            'songs.*.duration_seconds.max' => 'Song duration cannot exceed 2 hours.',
        ];
    }
} 