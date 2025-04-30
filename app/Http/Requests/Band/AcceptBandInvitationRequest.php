<?php

namespace App\Http\Requests\Band;

use App\Models\BandInvitation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AcceptBandInvitationRequest extends FormRequest
{
    protected ?BandInvitation $invitation = null;
    protected ?User $invitedUser = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the process
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'redirect_from' => ['nullable', 'boolean'],
        ]; // No validation needed here as token comes from route parameter
    }

    /**
     * Process the invitation acceptance
     *
     * @param string $token The invitation token from the route parameter
     */
    public function process(string $token): array
    {
        // Get and validate invitation
        $this->invitation = BandInvitation::where('token', $token)
//            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Get or create user
        $this->invitedUser = User::where('email', $this->invitation->email)->first();

        if (!auth()->check() && $this->invitedUser) {
            Auth::login($this->invitedUser);
            return $this->handleExistingUser();
        }

        if (!auth()->check()) {
            return $this->handleNewUser();
        }

        if (auth()->user()->email !== $this->invitation->email) {
            return [
                'success' => false,
                'redirect' => 'back',
                'message' => 'This invitation was sent to a different email address.'
            ];
        }

        return $this->handleExistingUser();
    }

    /**
     * Handle invitation acceptance for an existing user
     */
    protected function handleExistingUser(): array
    {
        $band = $this->invitation->band;

        if ($band->members()->where('user_id', $this->invitedUser?->id ?? auth()->id())->exists()) {
            return [
                'success' => false,
                'redirect' => route('bands.show', $band->id),
                'message' => "You have already joined the $band->name."
            ];
        }

        $band->members()->attach(
            $this->invitedUser?->id ?? auth()->id(),
            ['role' => $this->invitation->role]
        );

        // Mark as accepted and then delete
        $this->invitation->update(['accepted_at' => now()]);

        return [
            'success' => true,
            'redirect' => route('bands.show', $band->id),
            'message' => "You have successfully joined the $band->name."
        ];
    }

    /**
     * Handle invitation acceptance for a new user
     */
    protected function handleNewUser(): array
    {
        $emailVerified = $this->redirect_from && decrypt($this->redirect_from) === 'email';

        // Create a new user with a temporary password
        $tempPassword = Str::random(16);
        $this->invitedUser = User::create([
            'name' => explode('@', $this->invitation->email)[0], // Use email prefix as temporary name
            'email' => $this->invitation->email,
            'is_subscribed' => true,
            'email_verified_at' => $emailVerified ? now() : null,
            'password' => Hash::make($tempPassword),
            'password_set' => false, // Set this to false for new users
        ]);

        // Log the user in
        Auth::login($this->invitedUser);

        // Add them to the band
        $this->invitation->band->members()->attach(
            $this->invitedUser->id,
            ['role' => $this->invitation->role]
        );

        // Mark as accepted and then delete
        $this->invitation->update(['accepted_at' => now()]);
//        $this->invitation->delete();

        return [
            'success' => true,
            'redirect' => route('profile.complete'),
            'message' => 'Welcome to ' . $this->invitation->band->name . '! Please complete your profile setup.'
        ];
    }
}
