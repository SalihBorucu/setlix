<?php

namespace App\Http\Requests\Song;

use App\Models\Song;
use App\Services\SpotifyService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSongRequest extends FormRequest
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
            'spotify_link' => ['nullable', 'url'],
            'band_id' => ['required', 'exists:bands,id'],
            'name' => [
                'required_if:spotify_link,null',
                'max:255',
                Rule::unique('songs')->where(function ($query) {
                    return $query->where('band_id', $this->band_id);
                })
            ],
            'duration_seconds' => ['required_if:spotify_link,null', 'min:0'],
            'notes' => ['nullable', 'string'],
            'url' => ['nullable', 'url'],
            'artist' => ['nullable', 'string', 'max:255'],
            'bpm' => ['nullable', 'integer', 'min:0'],
            // New file upload rules
            'files' => ['nullable', 'array', 'max:10'], // Max 10 files
            'files.*.type' => ['required_with:files', 'string', 'in:lyrics,notes,chords,tabs,sheet_music,other'],
            'files.*.file' => [
                'required_with:files',
                'file',
                'mimes:pdf,txt',
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

    public function importFromSpotify(): Song
    {
        $service = new SpotifyService();
        $trackDetails = $service->getTrack($this->spotify_link);

        $song = Song::create([
            ...$trackDetails,
            'band_id' => $this->band_id,
        ]);

        return $song;
    }
}
