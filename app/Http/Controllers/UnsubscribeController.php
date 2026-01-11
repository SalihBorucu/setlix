<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    /**
     * Handle the unsubscribe request (signed URL)
     */
    public function unsubscribe(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Invalid or expired unsubscribe link.');
        }

        $user->update(['unsubscribed_from_marketing' => true]);

        return view('unsubscribe.success', [
            'userName' => $user->name,
        ]);
    }
}
