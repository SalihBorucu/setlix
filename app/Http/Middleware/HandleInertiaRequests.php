<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'trial' => $user ? [
                'isInTrial' => $user->isInTrialPeriod(),
                'remainingDays' => $user->getRemainingTrialDays(),
                'limitReached' => session('trial_limit_reached', false),
                'limitMessage' => session('trial_limit_message', ''),
                'isSubscribed' => $user->is_subscribed,
            ] : null,
        ];
    }
}
