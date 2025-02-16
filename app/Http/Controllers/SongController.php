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

class SongController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
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

        // Handle document upload if present
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('documents', 'public');
            $validated['document_path'] = $path;
            $validated['document_type'] = $request->file('document')->getClientOriginalExtension();
        }

        $song = Song::create($validated);

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
            'isAdmin' => $band->isAdmin(auth()->user())
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

        // Handle document upload if present
        if ($request->hasFile('document')) {
            // Delete old document if exists
            $song->deleteDocument();

            $path = $request->file('document')->store('documents', 'public');
            $validated['document_path'] = $path;
            $validated['document_type'] = $request->file('document')->getClientOriginalExtension();
        }

        $song->update($validated);

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
    public function downloadDocument(Band $band, Song $song): mixed
    {
        $this->authorize('view', $band);

        if (!$song->hasDocument()) {
            return back()->with('error', 'No document attached to this song.');
        }

        return Storage::disk('public')->download(
            $song->document_path,
            $song->name . '.' . $song->document_type
        );
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
