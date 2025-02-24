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
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Band\AcceptBandInvitationRequest;

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
                        'url' => route('invitations.accept', $invitation->token),
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

        // Check member limit
        if ($band->members()->count() >= 50) {
            return back()->withErrors(['error' => 'Band has reached the maximum member limit of 50.']);
        }

        // Check active invitations limit
        $activeInvitationsCount = $band->invitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->count();

        if ($activeInvitationsCount >= 10) {
            return back()->withErrors(['error' => 'Maximum of 10 pending invitations allowed.']);
        }

        // Check if user is already a member
        if ($band->members()->where('email', $validated['email'])->exists()) {
            return back()->withErrors(['email' => 'This user is already a member of the band.']);
        }

        // Check for existing invitation
        if ($band->invitations()->where('email', $validated['email'])->whereNull('accepted_at')->exists()) {
            return back()->withErrors(['email' => 'An invitation has already been sent to this email.']);
        }

        // Check invitation history and cooldown
        $lastInvitation = DB::table('band_invitation_history')
            ->where('band_id', $band->id)
            ->where('email', $validated['email'])
            ->orderBy('invited_at', 'desc')
            ->first();

        if ($lastInvitation && now()->subHours(24)->lt($lastInvitation->invited_at)) {
            return back()->withErrors(['email' => 'Please wait 24 hours before sending another invitation to this email.']);
        }

        // Record invitation attempt
        DB::table('band_invitation_history')->insert([
            'band_id' => $band->id,
            'email' => $validated['email'],
            'invited_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

    public function acceptInvitation(AcceptBandInvitationRequest $request, $token)
    {
        $result = $request->process($token);

        if ($result['redirect'] === 'back') {
            return back()->withErrors(['error' => $result['message']]);
        }

        return redirect($result['redirect'])
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
