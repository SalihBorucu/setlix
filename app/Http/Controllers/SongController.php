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
            'songs' => $band->songs()->orderBy('name')->get(),
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

        return Inertia::render('Songs/Show', [
            'band' => $band,
            'song' => $song,
            'isAdmin' => $band->isAdmin(auth()->user()),
            'document_path' => $song->document_path,
        ]);
    }

    /**
     * Show the form for editing the specified song.
     */
    public function edit(Band $band, Song $song): Response
    {
        $this->authorize('update', $band);

        return Inertia::render('Songs/Edit', [
            'band' => $band,
            'song' => $song
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

        // Delete associated document if exists
        $song->deleteDocument();

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
        collect($validated['songs'])->map(function ($song) use ($band, &$songs) {
            $songs[] = [
                'name' => $song['name'],
                'duration_seconds' => $song['duration_seconds'],
                'artist' => $song['artist'],
                'url' => $song['url'],
                'band_id' => $band->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        Song::insert($songs);

        return redirect()->route('songs.index', $band)
            ->with('success', count($songs) . ' songs added successfully.');
    }
}
