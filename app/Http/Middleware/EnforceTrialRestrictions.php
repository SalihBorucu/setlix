<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\BandSubscriptionService;

class EnforceTrialRestrictions
{
    public function __construct(private BandSubscriptionService $subscriptionService)
    {}

    public function handle($request, Closure $next)
    {
        $band = $request->route('band');
        
        if (!$band) {
            return $next($request);
        }

        $status = $this->subscriptionService->getStatus($band);
        
        if ($status['isReadOnly']) {
            if ($request->isMethod('GET')) {
                return $next($request);
            }
            return redirect()->back()->with('error', 'Your trial has expired. Band is in read-only mode.');
        }

        if ($status['isInTrial']) {
            $limits = $this->subscriptionService->checkTrialLimits($band);
            if ($limits['anyLimitReached'] && !$request->isMethod('GET')) {
                return redirect()->back()->with('error', 'Trial limits reached. Please subscribe to continue.');
            }
        }

        return $next($request);
    }
} 