<?php

namespace App\Http\Middleware;

use App\Models\Band;
use Closure;
use Illuminate\Http\Request;

class EnforceTrialLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->is_trial) {
            return $next($request);
        }

        // Check if trial has expired
        if ($user->hasTrialExpired()) {
            return redirect()->route('subscription.expired');
        }

        // Define trial limits based on the current route/action
        $limitReached = false;
        $limitMessage = '';

        // Band creation limit
//        if ($request->is('bands') && $request->isMethod('post')) {
//            if (!$user->canCreateMoreBands()) {
//                $limitReached = true;
//                $limitMessage = 'Free trial allows only one band. Please subscribe to create more.';
//            }
//        }

        // Song creation limit
        if ($request->is('*/songs') && $request->isMethod('post')) {
            $bandId = $request->route('band');
            $band = Band::find($bandId);

//            if ($band && $band->songs()->count() >= 10) {
//                $limitReached = true;
//                $limitMessage = 'Free trial allows maximum 10 songs. Please subscribe to add more.';
//            }
        }

        // Setlist creation limit
        if ($request->is('*/setlists') && $request->isMethod('post')) {
            $bandId = $request->route('band');
            $band = Band::find($bandId);

            if ($band && $band->setlists()->count() >= 3) {
                $limitReached = true;
                $limitMessage = 'Free trial allows maximum 3 setlists. Please subscribe to add more.';
            }
        }

        // Member invitation limit
        if ($request->is('*/members/invite') && $request->isMethod('post')) {
            $bandId = $request->route('band');
            $band = Band::find($bandId);

            if ($band) {
                $totalMembers = $band->members()->count() + $band->invitations()->count();
                if ($totalMembers >= 3) {
                    $limitReached = true;
                    $limitMessage = 'Free trial allows maximum 3 members. Please subscribe to add more.';
                }
            }
        }

        // If limit is reached, store the message and return
        if ($limitReached) {
            return back()->with([
                'trial_limit_reached' => true,
                'trial_limit_message' => $limitMessage
            ]);
        }

        return $next($request);
    }
}
