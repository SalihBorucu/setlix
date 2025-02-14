<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setlist\StoreSetlistRequest;
use App\Models\Band;
use App\Models\Setlist;
use Illuminate\Http\RedirectResponse;
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
        $setlist = Setlist::create($request->validated());

        // Attach songs if provided in song_order
        if ($request->song_order) {
            foreach ($request->song_order as $index => $songId) {
                $setlist->songs()->attach($songId, ['order' => $index]);
            }
            $setlist->updateTotalDuration();
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

        $setlist->load('songs');
        $setlist->formatted_created_at = $setlist->created_at->format('d M Y');

        return Inertia::render('Setlists/Show', [
            'band' => $band,
            'setlist' => $setlist->load('songs'),
            'isAdmin' => $band->isAdmin(auth()->user())
        ]);
    }

    /**
     * Show the form for editing the specified setlist.
     */
    public function edit(Band $band, Setlist $setlist): Response
    {
        $this->authorize('update', $band);

        return Inertia::render('Setlists/Edit', [
            'band' => $band,
            'setlist' => $setlist->load('songs'),
            'availableSongs' => $band->songs()->orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified setlist in storage.
     */
    public function update(StoreSetlistRequest $request, Band $band, Setlist $setlist): RedirectResponse
    {
        $setlist->update($request->validated());

        // Update song order if provided
        if ($request->song_order) {
            $setlist->songs()->detach(); // Remove all existing songs
            foreach ($request->song_order as $index => $songId) {
                $setlist->songs()->attach($songId, ['order' => $index]);
            }
            $setlist->updateTotalDuration();
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

        $setlist->songs()->detach();
        $setlist->delete();

        return redirect()->route('setlists.index', ['band' => $band->id]);
    }
}
