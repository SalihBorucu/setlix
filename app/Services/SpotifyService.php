<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SpotifyService
{
    private string $clientId;
    private string $clientSecret;

    public function __construct()
    {
        $this->clientId = config('services.spotify.client_id');
        $this->clientSecret = config('services.spotify.client_secret');
    }

    private function getAccessToken(): string
    {
        return Cache::remember('spotify_token', 3500, function () {
            $response = Http::asForm()
                ->post('https://accounts.spotify.com/api/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get Spotify access token');
            }

            return $response->json()['access_token'];
        });
    }

    private function extractPlaylistId(string $input): string
    {
        // Handle full Spotify URLs
        if (Str::contains($input, 'spotify.com/playlist/')) {
            preg_match('/playlist\/([a-zA-Z0-9]+)/', $input, $matches);
            return $matches[1] ?? '';
        }

        // Handle just the ID
        return $input;
    }

    /**
     * Extracts the Spotify track ID from a URL or URI.
     *
     * @param string $input The Spotify track URL, URI, or just the ID.
     * @return string The extracted track ID.
     * @throws \InvalidArgumentException If the track ID cannot be extracted.
     */
    private function extractTrackId(string $input): string
    {
        // Handle full Spotify track URLs (e.g., https://open.spotify.com/track/TRACK_ID?si=...)
        if (Str::contains($input, 'open.spotify.com/track/')) {
            $path = parse_url($input, PHP_URL_PATH); // Gets '/track/TRACK_ID'
            $trackId = Str::afterLast($path, '/');
            if (!empty($trackId)) {
                return $trackId;
            }
        }

        // Handle Spotify URIs (e.g., spotify:track:TRACK_ID)
        if (Str::startsWith($input, 'spotify:track:')) {
            return Str::after($input, 'spotify:track:');
        }

        // Assume it's just the ID if it matches Spotify ID format (alphanumeric, 22 chars)
        if (preg_match('/^[a-zA-Z0-9]{22}$/', $input)) {
            return $input;
        }

        throw new \InvalidArgumentException('Invalid Spotify track URL, URI or ID format: ' . $input);
    }

    public function getTrack(string $trackUrl): array
    {
        try {
            $accessToken = $this->getAccessToken();
            $trackId = $this->extractTrackId($trackUrl);
        } catch (\Exception $e) {
            // Re-throw or handle initialization errors (token, ID extraction)
            throw new \Exception('Error preparing to fetch track: ' . $e->getMessage(), 0, $e);
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ])->get("https://api.spotify.com/v1/tracks/{$trackId}", [
            'fields' => 'name,duration_ms,artists,external_urls,lyrics'
        ]);

        if (!$response->successful()) {
            // Log::error('Spotify Get Track API Error: ' . $response->body());
            throw new \Exception('Failed to get track details from Spotify. Status: ' . $response->status() . ' Body: ' . $response->body());
        }

        $trackData = $response->json();
        if (empty($trackData) || !isset($trackData['name'])) {
            throw new \Exception('Received empty or invalid track data from Spotify for ID: ' . $trackId);
        }

        $durationSeconds = floor($trackData['duration_ms'] / 1000);

        return [
            'name' => $trackData['name'],
            'url' => $trackData['external_urls']['spotify'] ?? null,
            'artist' => $trackData['artists'][0]['name'] ?? null,
            'duration' => sprintf('%d:%02d', floor($durationSeconds / 60), $durationSeconds % 60),
            'duration_seconds' => $durationSeconds
        ];
    }

    public function getPlaylistTracks(string $playlistUrl)
    {
        $playlistId = $this->extractPlaylistId($playlistUrl);

        if (empty($playlistId)) {
            throw new \Exception('Invalid playlist URL or ID');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken()
        ])->get("https://api.spotify.com/v1/playlists/{$playlistId}/tracks", [
            'fields' => 'items(track(name,duration_ms,artists,external_urls,lyrics))'
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch playlist tracks');
        }

        return collect($response->json()['items'])->map(function ($item) {
            $durationSeconds = floor($item['track']['duration_ms'] / 1000);

            return [
                'name' => $item['track']['name'],
                'url' => $item['track']['external_urls']['spotify'] ?? null,
                'artist' => $item['track']['artists'][0]['name'] ?? null,
                'duration' => sprintf('%d:%02d', floor($durationSeconds / 60), $durationSeconds % 60),
                'duration_seconds' => $durationSeconds
            ];
        });
    }
}
