<?php

namespace App\Http\Controllers;

use App\Mail\BandInvitationMail;
use App\Models\Band;
use App\Models\BandInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BandMemberController extends Controller
{
    public function index(Band $band)
    {
        $this->authorize('view', $band);

        return Inertia::render('Bands/Members/Index', [
            'band' => $band,
            'members' => $band->members()->get()->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'avatar' => $member->avatar,
                    'role' => $member->pivot->role,
                ];
            }),
            'pendingInvitations' => $band->invitations()
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->get()
                ->map(function ($invitation) {
                    return [
                        'id' => $invitation->id,
                        'email' => $invitation->email,
                        'role' => $invitation->role,
                        'created_at' => $invitation->created_at->diffForHumans(),
                    ];
                }),
            'isAdmin' => $band->members()->where('user_id', auth()->id())->first()->pivot->role === 'admin',
        ]);
    }

    public function invite(Request $request, Band $band)
    {
        $this->authorize('update', $band);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:member,admin'],
        ]);

        // Check if user is already a member
        if ($band->members()->where('email', $validated['email'])->exists()) {
            return back()->withErrors(['email' => 'This user is already a member of the band.']);
        }

        // Check for existing invitation
        if ($band->invitations()->where('email', $validated['email'])->whereNull('accepted_at')->exists()) {
            return back()->withErrors(['email' => 'An invitation has already been sent to this email.']);
        }

        $invitation = $band->invitations()->create($validated);

        // Send invitation email
        Mail::to($validated['email'])->send(new BandInvitationMail($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function remove(Band $band, User $member)
    {
        $this->authorize('update', $band);

        // Prevent removing the last admin
        if ($member->isAdminOf($band) && $band->members()->where('role', 'admin')->count() === 1) {
            return back()->withErrors(['error' => 'Cannot remove the last admin.']);
        }

        $band->members()->detach($member->id);

        return back()->with('success', 'Member removed successfully.');
    }

    public function cancelInvitation(Band $band, BandInvitation $invitation)
    {
        $this->authorize('update', $band);

        $invitation->delete();

        return back()->with('success', 'Invitation cancelled successfully.');
    }

    public function acceptInvitation(Request $request, $token)
    {
        $invitation = BandInvitation::where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        if (!auth()->check()) {
            // Create a new user with a temporary password
            $tempPassword = Str::random(16);
            $user = User::create([
                'name' => explode('@', $invitation->email)[0], // Use email prefix as temporary name
                'email' => $invitation->email,
                'password' => Hash::make($tempPassword),
                'password_set' => false, // Set this to false for new users
            ]);

            // Log the user in
            Auth::login($user);

            // Add them to the band
            $invitation->band->members()->attach($user->id, ['role' => $invitation->role]);
            $invitation->update(['accepted_at' => now()]);

            // Redirect to complete profile setup
            return redirect()->route('profile.complete')
                ->with('success', 'Welcome to ' . $invitation->band->name . '! Please complete your profile setup.');
        }

        if (auth()->user()->email !== $invitation->email) {
            return back()->withErrors(['error' => 'This invitation was sent to a different email address.']);
        }

        $invitation->band->members()->attach(auth()->id(), ['role' => $invitation->role]);
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('bands.show', $invitation->band)
            ->with('success', 'You have successfully joined the band.');
    }
}
