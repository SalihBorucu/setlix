<?php

namespace App\Http\Controllers;

use App\Http\Requests\Song\StoreSongRequest;
use App\Http\Requests\Song\BulkStoreSongRequest;
use App\Models\Band;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use App\Services\SongFileService;
use App\Models\SongFile;

class SongController extends Controller
{
    protected SongFileService $fileService;

    /**
     * Create a new controller instance.
     */
    public function __construct(SongFileService $fileService)
    {
        $this->middleware(['auth']);
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the band's songs.
     */
    public function index(Band $band): Response
    {
        $this->authorize('view', $band);

        return Inertia::render('Songs/Index', [
            'band' => $band->load('members'),
            'songs' => $band->songs()
                ->with('files')
                ->orderBy('name')
                ->get()
                ->map(function ($song) {
                    return array_merge($song->toArray(), [
                        'formatted_duration' => $song->formatted_duration,
                    ]);
                }),
            'isAdmin' => $band->isAdmin(auth()->user())
        ]);
    }

    /**
     * Show the form for creating a new song.
     */
    public function create(Band $band): Response
    {
        $this->authorize('update', $band);

        return Inertia::render('Songs/Create', [
            'band' => $band
        ]);
    }

    /**
     * Store a newly created song in storage.
     */
    public function store(StoreSongRequest $request): RedirectResponse
    {
        if ($request->spotify_link) {
            $song = $request->importFromSpotify();
        } else {
            $validated = $request->validated();
            $files = $validated['files'] ?? [];
            unset($validated['files']);

            $song = Song::create($validated);

            // Handle file uploads
            if (!empty($files)) {
                foreach ($files as $fileData) {
                    $this->fileService->store(
                        $song,
                        $fileData['file'],
                        $fileData['type']
                    );
                }
            }
        }

        return redirect()->route('songs.show', [
            'band' => $song->band_id,
            'song' => $song->id
        ]);
    }

    /**
     * Display the specified song.
     */
    public function show(Band $band, Song $song): Response
    {
        $this->authorize('view', $band);

        // Load the files and setlists relationships
        $song->load(['files', 'setlists' => function ($query) {
            $query->withCount('songs');
        }]);

        return Inertia::render('Songs/Show', [
            'band' => $band,
            'song' => array_merge($song->toArray(), [
                'formatted_duration' => $song->formatted_duration,
                'formatted_created_at' => $song->created_at->diffForHumans(),
            ]),
            'isAdmin' => $band->isAdmin(auth()->user()),
        ]);
    }

    /**
     * Show the form for editing the specified song.
     */
    public function edit(Band $band, Song $song): Response
    {
        $this->authorize('update', $band);

        // Load the files relationship
        $song->load('files');

        return Inertia::render('Songs/Edit', [
            'band' => $band,
            'song' => array_merge($song->toArray(), [
                'formatted_duration' => $song->formatted_duration,
            ]),
        ]);
    }

    /**
     * Update the specified song in storage.
     */
    public function update(StoreSongRequest $request, Band $band, Song $song): RedirectResponse
    {
        $validated = $request->validated();
        $files = $validated['files'] ?? [];
        unset($validated['files']);

        $song->update($validated);

        // Handle file uploads
        if (!empty($files)) {
            foreach ($files as $fileData) {
                $this->fileService->store(
                    $song,
                    $fileData['file'],
                    $fileData['type']
                );
            }
        }

        return redirect()->route('songs.show', [
            'band' => $band->id,
            'song' => $song->id
        ]);
    }

    /**
     * Remove the specified song from storage.
     */
    public function destroy(Band $band, Song $song): RedirectResponse
    {
        $this->authorize('update', $band);

        // Delete all associated files
        foreach ($song->files as $file) {
            $this->fileService->delete($file);
        }

        $song->delete();

        return redirect()->route('songs.index', ['band' => $band->id]);
    }

    /**
     * Download the song's document.
     */
    public function downloadDocument(Band $band, Song $song, SongFile $file): mixed
    {
        $this->authorize('view', $band);

        try {
            $url = $this->fileService->getTemporaryUrl($file);
            return redirect($url);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate download link.');
        }
    }

    /**
     * Show the form for bulk creating songs.
     */
    public function bulkCreate(Band $band): Response
    {
        $this->authorize('create', [Song::class, $band]);

        return Inertia::render('Songs/BulkCreate', [
            'band' => $band
        ]);
    }

    /**
     * Store multiple songs in storage.
     */
    public function bulkStore(BulkStoreSongRequest $request, Band $band): RedirectResponse
    {
        $validated = $request->validated();

        $songs = [];
        foreach ($validated['songs'] as $song) {
            $songs[] = [
                'name' => $song['name'],
                'duration_seconds' => $song['duration_seconds'],
                'artist' => $song['artist'],
                'url' => $song['url'],
                'band_id' => $band->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $existingSongs = $band->songs;
        $songs = array_filter($songs, function ($song) use ($existingSongs, $band) {
            return !$existingSongs->where('name', $song['name'])->first();
        });

        Song::insert($songs);

        return redirect()->route('songs.index', $band)
            ->with('success', count($songs) . ' songs added successfully.');
    }

    /**
     * Delete a song file.
     */
    public function deleteFile(Band $band, Song $song, SongFile $file): RedirectResponse
    {
        $this->authorize('update', $band);

        try {
            $this->fileService->delete($file);
            return back()->with('success', 'File deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete file.');
        }
    }

    /**
     * Download a song file.
     */
    public function downloadFile(Band $band, Song $song, SongFile $file): mixed
    {
        $this->authorize('view', $band);

        try {
            $url = $this->fileService->getTemporaryUrl($file);
            return redirect($url);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate download link.');
        }
    }
}
