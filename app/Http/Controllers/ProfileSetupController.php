<?php

namespace App\Http\Controllers;

use App\Models\BandInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileSetupController extends Controller
{
    public function show()
    {
        if (auth()->user()->password_set) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/CompleteProfile');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        $user->update([
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'password_set' => true,
        ]);

        BandInvitation::where('email', $user->email)->whereNotNull('accepted_at')->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Profile setup completed successfully!');
    }
}
