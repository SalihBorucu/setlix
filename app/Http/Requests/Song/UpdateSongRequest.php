<?php

namespace App\Http\Requests\Song;

use App\Models\Song;
use App\Services\SongFileService;
use App\Services\SpotifyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
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
            'name' => [
                'required',
                'max:255'
            ],
            'duration_seconds' => ['required', 'min:0'],
            'notes' => ['nullable', 'string'],
            'url' => ['nullable', 'url'],
            'artist' => ['nullable', 'string', 'max:255'],
            'bpm' => ['nullable', 'integer', 'min:0'],
            // New file upload rules
            'files' => ['nullable', 'array', 'max:10'], // Max 10 files
            'files.*.type' => ['required_with:files', 'string', 'in:lyrics,notes,chords,tabs,sheet_music,other'],
            'files.*.instrument' => ['required_with:files', 'string'],
            'files.*.file' => [
                'required_with:files',
                'file',
                'mimes:pdf,txt,musicxml,xml',
                'max:10240', // 10MB
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
            'files.max' => 'You can upload a maximum of 10 files.',
            'files.*.file.max' => 'Each file must not exceed 10MB.',
            'files.*.file.mimes' => 'Files must be PDF or TXT format.',
            'files.*.type.in' => 'File type must be one of: lyrics, notes, chords, tabs, sheet music, or other.',
        ];
    }
}
