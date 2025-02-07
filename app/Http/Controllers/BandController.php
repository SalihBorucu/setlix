<?php

namespace App\Http\Controllers;

use App\Http\Requests\Band\StoreBandRequest;
use App\Models\Band;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BandController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the bands.
     */
    public function index(): Response
    {
        return Inertia::render('Bands/Index', [
            'bands' => Auth::user()->bands()
                ->with('members')
                ->withCount(['songs', 'setlists', 'members'])
                ->paginate(9)
                ->through(function ($band) {
                    return [
                        'id' => $band->id,
                        'name' => $band->name,
                        'description' => $band->description,
                        'cover_image' => $band->cover_image,
                        'role' => $band->members->where('id', Auth::id())->first()->pivot->role,
                        'members_count' => $band->members_count,
                        'songs_count' => $band->songs_count,
                        'setlists_count' => $band->setlists_count,
                    ];
                })
        ]);
    }

    /**
     * Show the form for creating a new band.
     */
    public function create(): Response
    {
        return Inertia::render('Bands/Create');
    }

    /**
     * Store a newly created band in storage.
     */
    public function store(StoreBandRequest $request): RedirectResponse
    {
        $band = Band::create($request->validated());
        
        // Add the creator as an admin
        $band->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()->route('bands.show', $band);
    }

    /**
     * Display the specified band.
     */
    public function show(Band $band): Response
    {
        $this->authorize('view', $band);

        $band->load('members');
        $band->loadCount(['songs', 'setlists', 'members']);

        return Inertia::render('Bands/Show', [
            'band' => array_merge($band->toArray(), [
                'songs_count' => $band->songs_count,
                'setlists_count' => $band->setlists_count,
                'members_count' => $band->members_count
            ]),
            'isAdmin' => $band->isAdmin(Auth::user())
        ]);
    }

    /**
     * Show the form for editing the specified band.
     */
    public function edit(Band $band): Response
    {
        $this->authorize('update', $band);

        return Inertia::render('Bands/Edit', [
            'band' => $band
        ]);
    }

    /**
     * Update the specified band in storage.
     */
    public function update(StoreBandRequest $request, Band $band): RedirectResponse
    {
        $this->authorize('update', $band);

        $band->update($request->validated());

        return redirect()->route('bands.show', $band);
    }

    /**
     * Remove the specified band from storage.
     */
    public function destroy(Band $band): RedirectResponse
    {
        $this->authorize('delete', $band);

        $band->delete();

        return redirect()->route('bands.index');
    }
}
