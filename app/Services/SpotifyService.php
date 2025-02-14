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
