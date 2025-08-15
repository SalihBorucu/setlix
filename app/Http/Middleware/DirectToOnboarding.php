<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DirectToOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return Response|RedirectResponse|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle($request, Closure $next)
    {
        if (session()->get('onboarding_completed_once') || !$request->user()?->is_trial) {
            return $next($request);
        }

        $bands = $request->user()->bands()->with(['members', 'songs', 'setlists'])->get();

        if ($bands->count() === 0) {
            return redirect()->route('bands.create')->with('message', "Let's get started! Create your first band.");
        }

        if ($bands->pluck('songs')->flatten()->count() === 0) {
            $band = $request->user()->bands->first();
            return redirect()->route('songs.bulk-create', $band->id)->with('message', "Now it's time to add your first song. You can add multiple songs at once.");
        }

        if ($bands->pluck('setlists')->flatten()->count() === 0) {
            $band = $request->user()->bands->first();
            return redirect()->route('setlists.create', $band->id)->with('message', "Let's get started! Create your first setlist.");
        }

        if ($bands->pluck('members')->flatten()->count() === 1 && $request->user()->created_at > now()->addDay()) {
            session()->put('onboarding_completed_once', true);
            $band = $request->user()->bands->first();
            return redirect()->route('bands.members.index', $band->id)->with('message', 'Would you like to invite your band members? They can join your band at any time at no cost.');
        }

        return $next($request);
    }
}
