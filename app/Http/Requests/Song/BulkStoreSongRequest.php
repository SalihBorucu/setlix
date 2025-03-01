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
     */
    public function rules(): array
    {
        // Get current song count and check trial status
        $band = Band::findOrFail($this->route('band')->id);
        $currentSongCount = $band->songs()->count();
        $isSubscribed = $this->user()->trial?->isSubscribed ?? false;
        
        // If on trial, limit total songs to 10
        $maxNewSongs = $isSubscribed ? 200 : (10 - $currentSongCount);
        
        return [
            'songs' => ['required', 'array', 'min:1', "max:{$maxNewSongs}"],
            'songs.*.name' => ['required', 'string', 'max:255'],
            'songs.*.duration_seconds' => ['required', 'integer', 'min:1', 'max:7200'], // max 2 hours
            'songs.*.url' => ['nullable', 'url', 'max:2048'],
            'songs.*.artist' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $isSubscribed = $this->user()->trial?->isSubscribed ?? false;
        
        return [
            'songs.required' => 'At least one song is required.',
            'songs.max' => $isSubscribed 
                ? 'You can only add up to 200 songs at once.'
                : 'Free trial allows maximum 10 songs total. Please subscribe to add more songs.',
            'songs.*.name.required' => 'Each song must have a name.',
            'songs.*.name.max' => 'Song names cannot be longer than 255 characters.',
            'songs.*.duration_seconds.required' => 'Each song must have a duration.',
            'songs.*.duration_seconds.min' => 'Song duration must be at least 1 second.',
            'songs.*.duration_seconds.max' => 'Song duration cannot exceed 2 hours.',
            'songs.*.url.url' => 'The URL must be a valid URL.',
            'songs.*.url.max' => 'The URL cannot be longer than 2048 characters.',
            'songs.*.artist.max' => 'Artist name cannot be longer than 255 characters.',
        ];
    }
}
