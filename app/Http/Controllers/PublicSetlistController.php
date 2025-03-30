<?php

namespace App\Http\Controllers;

use App\Models\Setlist;
use App\Http\Requests\PublicSetlist\SubmitRequest;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicSetlistController extends Controller
{
    /**
     * Display the public setlist view.
     */
    public function show(string $slug): Response
    {
        $setlist = Setlist::where('public_slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        // Load items with songs and their files
        $setlist->load([
            'items.song.files',
            'band'
        ]);

        // Get all available songs (excluding ones already in the setlist)
        $availableSongs = $setlist->band->songs()
            ->whereNotIn('id', $setlist->items()
                ->where('type', 'song')
                ->pluck('song_id'))
            ->orderBy('name')
            ->get();

        return Inertia::render('PublicSetlist/Show', [
            'setlist' => $setlist,
            'targetDuration' => $setlist->target_duration,
            'availableSongs' => $availableSongs
        ]);
    }

    /**
     * Submit the client's setlist changes.
     */
    public function submit(SubmitRequest $request, string $slug): Response
    {
        $setlist = Setlist::where('public_slug', $slug)
            ->where('is_public', true)
            ->firstOrFail();

        // Calculate total duration of submitted items
        $totalDuration = collect($request->validated()['items'])->sum('duration_seconds');
        
        // Check if total duration exceeds target
        if ($totalDuration > $setlist->target_duration) {
            return back()->withErrors([
                'duration' => 'The total duration exceeds the target duration. Please adjust your selection.'
            ]);
        }

        // Delete existing items
        $setlist->items()->delete();

        // Create new items
        foreach ($request->validated()['items'] as $item) {
            $setlist->items()->create([
                'type' => $item['type'],
                'song_id' => $item['song_id'] ?? null,
                'title' => $item['title'] ?? null,
                'duration_seconds' => $item['duration_seconds'],
                'notes' => $item['notes'] ?? null,
                'order' => $item['order']
            ]);
        }

        // Update setlist with client info
        $setlist->update([
            'client_name' => $request->validated()['client_name'],
            'client_email' => $request->validated()['client_email'],
            'submitted_at' => now(),
            'is_public' => false
        ]);

        return Inertia::render('PublicSetlist/ThankYou', [
            'message' => 'Thank you for submitting your setlist preferences!'
        ]);
    }

    /**
     * Generate a public slug for a setlist.
     */
    public static function generatePublicSlug(): string
    {
        do {
            $slug = Str::random(16);
        } while (Setlist::where('public_slug', $slug)->exists());

        return $slug;
    }
}
