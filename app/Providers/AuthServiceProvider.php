<?php

namespace App\Providers;

use App\Models\Band;
use App\Models\Song;
use App\Policies\BandPolicy;
use App\Policies\SongPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Band::class => BandPolicy::class,
        Song::class => SongPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
} 