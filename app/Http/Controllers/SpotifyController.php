<?php

namespace App\Http\Controllers;

use App\Services\SpotifyService;
use Illuminate\Http\Request;

class SpotifyController extends Controller
{
    private SpotifyService $spotifyService;

    public function __construct(SpotifyService $spotifyService)
    {
        $this->spotifyService = $spotifyService;
    }

    public function getPlaylistTracks(Request $request)
    {
        $request->validate([
            'playlist_url' => 'required|string'
        ]);

        try {
            $tracks = $this->spotifyService->getPlaylistTracks($request->playlist_url);
            return response()->json(['tracks' => $tracks]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
} 