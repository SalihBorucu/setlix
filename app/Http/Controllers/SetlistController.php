<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setlist\StoreSetlistRequest;
use App\Models\Band;
use App\Models\Setlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SetlistController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the band's setlists.
     */
    public function index(Band $band): Response
    {
        $this->authorize('view', $band);

        return Inertia::render('Setlists/Index', [
            'band' => $band->load('members'),
            'setlists' => $band->setlists()
                ->with('songs')
                ->orderBy('name')
                ->get(),
            'isAdmin' => $band->isAdmin(auth()->user())
        ]);
    }

    /**
     * Show the form for creating a new setlist.
     */
    public function create(Band $band): Response
    {
        $this->authorize('update', $band);

        return Inertia::render('Setlists/Create', [
            'band' => $band,    
            'songs' => $band->songs()->orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created setlist in storage.
     */
    public function store(StoreSetlistRequest $request): RedirectResponse
    {
        $setlist = Setlist::create([
            'band_id' => $request->band_id,
            'name' => $request->name,
            'description' => $request->description,
            'target_duration' => $request->target_duration_seconds,
            'total_duration' => $request->total_duration
        ]);

        // Create setlist items
        foreach ($request->items as $index => $item) {
            $setlist->items()->create([
                'type' => $item['type'],
                'song_id' => $item['song_id'] ?? null,
                'title' => $item['title'] ?? null,
                'duration_seconds' => $item['duration_seconds'],
                'notes' => $item['notes'] ?? null,
                'order' => $index
            ]);
        }

        return redirect()->route('setlists.show', [
            'band' => $setlist->band_id,
            'setlist' => $setlist->id
        ]);
    }

    /**
     * Display the specified setlist.
     */
    public function show(Band $band, Setlist $setlist): Response
    {
        $this->authorize('view', $band);

        // Load items with songs and their files
        $setlist->load([
            'items.song.files', // Load files for songs
        ]);
        
        $setlist->formatted_created_at = $setlist->created_at->format('d M Y');

        return Inertia::render('Setlists/Show', [
            'band' => $band,
            'setlist' => $setlist,
            'isAdmin' => $band->isAdmin(auth()->user())
        ]);
    }

    /**
     * Show the form for editing the specified setlist.
     */
    public function edit(Band $band, Setlist $setlist): Response
    {
        $this->authorize('update', $band);

        // Load items with their songs
        $setlist->load(['items.song']);

        // Get all available songs (excluding ones already in the setlist)
        $availableSongs = $band->songs()
            ->whereNotIn('id', $setlist->items()
                ->where('type', 'song')
                ->pluck('song_id'))
            ->orderBy('name')
            ->get();

        return Inertia::render('Setlists/Edit', [
            'band' => $band,
            'setlist' => $setlist,
            'availableSongs' => $availableSongs
        ]);
    }

    /**
     * Update the specified setlist in storage.
     */
    public function update(StoreSetlistRequest $request, Band $band, Setlist $setlist): RedirectResponse
    {
        $setlist->update([
            'name' => $request->name,
            'description' => $request->description,
            'target_duration' => $request->target_duration_seconds,
            'total_duration' => $request->total_duration
        ]);

        // Remove existing items
        $setlist->items()->delete();

        // Create new items
        foreach ($request->items as $index => $item) {
            $setlist->items()->create([
                'type' => $item['type'],
                'song_id' => $item['song_id'] ?? null,
                'title' => $item['title'] ?? null,
                'duration_seconds' => $item['duration_seconds'],
                'notes' => $item['notes'] ?? null,
                'order' => $index
            ]);
        }

        return redirect()->route('setlists.show', [
            'band' => $band->id,
            'setlist' => $setlist->id
        ]);
    }

    /**
     * Remove the specified setlist from storage.
     */
    public function destroy(Band $band, Setlist $setlist): RedirectResponse
    {
        $this->authorize('update', $band);

        // Delete items (will happen automatically due to cascadeOnDelete)
        $setlist->delete();

        return redirect()->route('setlists.index', ['band' => $band->id]);
    }

    /**
     * Make a setlist public and generate a shareable link.
     */
    public function makePublic(Band $band, Setlist $setlist): JsonResponse
    {
        $this->authorize('update', $band);

        $setlist->update([
            'is_public' => true,
            'public_slug' => PublicSetlistController::generatePublicSlug()
        ]);

        return response()->json([
            'message' => 'Setlist is now public. Share the link with your client.',
            'setlist' => $setlist
        ]);
    }

    /**
     * Make a setlist private.
     */
    public function makePrivate(Band $band, Setlist $setlist): JsonResponse
    {
        $this->authorize('update', $band);

        $setlist->update([
            'is_public' => false,
            'public_slug' => null
        ]);

        return response()->json([
            'message' => 'Setlist is now private.',
            'setlist' => $setlist
        ]);
    }
}
