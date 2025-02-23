<?php

namespace App\Http\Controllers;

use App\Http\Requests\Band\StoreBandRequest;
use App\Models\Band;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\ImageService;
use App\Services\TrialService;
use Illuminate\Http\Request;

class BandController extends Controller
{
    protected $trialService;

    public function __construct(
        private ImageService $imageService,
        TrialService $trialService
    ) {
        $this->trialService = $trialService;
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
        $user = $request->user();

        // Check if user can create more bands
        if (!$user->is_subscribed && $user->bands()->count() >= 1) {
            return back()->with('error', 'Free trial allows only one band. Please subscribe to create more.');
        }

        // Start trial on first band creation
        $this->trialService->startTrial($user);

        $validated = $request->validated();
        
        // Create band with basic info
        $band = Band::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);
        
        // Process cover image if provided
        if ($request->hasFile('cover_image')) {
            $imagePaths = $this->imageService->processBandCoverImage($request->file('cover_image'));
            $band->update($imagePaths);
        }
        
        // Attach the creator as an admin
        $band->members()->attach($request->user(), ['role' => 'admin']);
        
        return redirect()->route('bands.show', $band)
            ->with('success', 'Band created successfully.');
    }

    /**
     * Display the specified band.
     */
    public function show(Band $band): Response
    {
        $this->authorize('view', $band);

        // Load members with pivot data
        $band->load(['members' => function ($query) {
            $query->select('users.*', 'band_user.role');
        }]);
        $band->loadCount(['songs', 'setlists', 'members']);

        $currentUser = Auth::user();
        $currentUserRole = $band->members->where('id', $currentUser->id)->first()->pivot->role;

        return Inertia::render('Bands/Show', [
            'band' => array_merge($band->toArray(), [
                'songs_count' => $band->songs_count,
                'setlists_count' => $band->setlists_count,
                'members_count' => $band->members_count,
                'members' => $band->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'avatar' => $member->avatar,
                        'pivot' => [
                            'role' => $member->pivot->role
                        ]
                    ];
                })
            ]),
            'isAdmin' => $band->isAdmin($currentUser),
            'currentUserRole' => $currentUserRole
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
        
        $validated = $request->validated();
        
        // Update basic info
        $band->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);
        
        // Process new cover image if provided
        if ($request->hasFile('cover_image')) {
            // Delete old images
            $this->imageService->deleteBandCoverImages(
                $band->cover_image_path,
                $band->cover_image_thumbnail_path,
                $band->cover_image_small_path
            );
            
            // Process and store new images
            $imagePaths = $this->imageService->processBandCoverImage($request->file('cover_image'));
            $band->update($imagePaths);
        }
        
        return back()->with('success', 'Band updated successfully.');
    }

    /**
     * Remove the specified band from storage.
     */
    public function destroy(Band $band): RedirectResponse
    {
        $this->authorize('delete', $band);
        
        // Delete cover images if they exist
        $this->imageService->deleteBandCoverImages(
            $band->cover_image_path,
            $band->cover_image_thumbnail_path,
            $band->cover_image_small_path
        );
        
        $band->delete();
        
        return redirect()->route('dashboard')
            ->with('success', 'Band deleted successfully.');
    }

    /**
     * Allow a member to leave the band.
     */
    public function leave(Band $band): RedirectResponse
    {
        $user = Auth::user();
        
        // Check if user is a member and not an admin
        if (!$band->hasMember($user)) {
            abort(403, 'You are not a member of this band.');
        }
        
        if ($band->isAdmin($user)) {
            abort(403, 'Admins cannot leave the band. Transfer admin role first.');
        }
        
        // Remove the user from the band
        $band->members()->detach($user->id);
        
        return redirect()->route('dashboard')
            ->with('success', 'You have left the band successfully.');
    }

    public function addMember(Request $request, Band $band)
    {
        $user = $request->user();

        // Check member limit for trial
        if (!$user->is_subscribed && $band->members()->count() >= 3) {
            return back()->with('error', 'Free trial allows maximum 3 members. Please subscribe to add more.');
        }

        // ... rest of add member logic
    }
}
