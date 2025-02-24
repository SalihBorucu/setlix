<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SetlistController;
use App\Http\Controllers\BandMemberController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\EnsureProfileIsComplete;
use App\Services\SpotifyService;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;

Route::get('/test', function () {
    Bugsnag::notifyException(new RuntimeException("Test error"));
});

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return redirect('/login');
})->middleware(EnsureProfileIsComplete::class);

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', EnsureProfileIsComplete::class])
    ->name('dashboard');

Route::middleware(['auth', EnsureProfileIsComplete::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Band routes
    Route::resource('bands', BandController::class)->except(['index']);
    Route::delete('bands/{band}/leave', [BandController::class, 'leave'])->name('bands.leave');

    // Song routes (nested under bands)
    Route::get('/bands/{band}/songs', [SongController::class, 'index'])->name('songs.index');
    Route::get('/bands/{band}/songs/create', [SongController::class, 'create'])->name('songs.create');
    Route::get('/bands/{band}/songs/bulk-create', [SongController::class, 'bulkCreate'])->name('songs.bulk-create');
    Route::post('/bands/{band}/songs/bulk-store', [SongController::class, 'bulkStore'])->name('songs.bulk-store');
    Route::post('/bands/{band}/songs', [SongController::class, 'store'])->name('songs.store');
    Route::get('/bands/{band}/songs/{song}', [SongController::class, 'show'])->name('songs.show');
    Route::get('/bands/{band}/songs/{song}/edit', [SongController::class, 'edit'])->name('songs.edit');
    Route::patch('/bands/{band}/songs/{song}', [SongController::class, 'update'])->name('songs.update');
    Route::delete('/bands/{band}/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
    Route::get('/bands/{band}/songs/{song}/document', [SongController::class, 'downloadDocument'])->name('songs.document');
    Route::delete('/bands/{band}/songs/{song}/files/{file}', [SongController::class, 'deleteFile'])
        ->name('songs.files.destroy');
    Route::get('/bands/{band}/songs/{song}/files/{file}/download', [SongController::class, 'downloadFile'])
        ->name('songs.files.download');

    // Setlist routes (nested under bands)
    Route::get('/bands/{band}/setlists', [SetlistController::class, 'index'])->name('setlists.index');
    Route::get('/bands/{band}/setlists/create', [SetlistController::class, 'create'])->name('setlists.create');
    Route::get('/bands/{band}/setlists/{setlist}', [SetlistController::class, 'show'])->name('setlists.show');
    Route::get('/bands/{band}/setlists/{setlist}/edit', [SetlistController::class, 'edit'])->name('setlists.edit');
    Route::put('/bands/{band}/setlists/{setlist}', [SetlistController::class, 'update'])->name('setlists.update');
    Route::delete('/bands/{band}/setlists/{setlist}', [SetlistController::class, 'destroy'])->name('setlists.destroy');

    // Band Member Management
    Route::get('/bands/{band}/members', [BandMemberController::class, 'index'])->name('bands.members.index');
    Route::post('/bands/{band}/members/invite', [BandMemberController::class, 'invite'])->name('bands.members.invite');
    Route::delete('/bands/{band}/members/{member}', [BandMemberController::class, 'remove'])->name('bands.members.remove');
    Route::delete('/bands/{band}/invitations/{invitation}', [BandMemberController::class, 'cancelInvitation'])->name('bands.members.cancel-invitation');

    // Profile setup routes
    Route::get('/profile/complete', [ProfileSetupController::class, 'show'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileSetupController::class, 'update'])->name('profile.complete.update');

    Route::post('/spotify/playlist-tracks', [SpotifyController::class, 'getPlaylistTracks'])
        ->name('spotify.playlist-tracks');

    // Subscription routes - group them together
    Route::controller(SubscriptionController::class)->group(function () {
        Route::get('/subscription/expired', 'expired')->name('subscription.expired');
        Route::post('/subscription/process', 'process')->name('subscription.process');
    });
});

Route::middleware([
    'auth',
    \App\Http\Middleware\EnforceTrialLimits::class
])->group(function () {
    // Group all routes that need trial limits
    Route::post('/bands/{band}/songs', [SongController::class, 'store'])->name('songs.store');
    Route::post('/bands/{band}/setlists', [SetlistController::class, 'store'])->name('setlists.store');
    // ... other routes that need trial limits ...
});

// Public route for accepting invitations
Route::get('/invitations/{token}', [BandMemberController::class, 'acceptInvitation'])->name('invitations.accept');

Route::middleware(['auth'])->group(function () {
    // Subscription routes
    Route::get('bands/{band}/subscribe', [SubscriptionController::class, 'checkout'])
        ->name('bands.subscribe');
    Route::post('/subscription/create', [SubscriptionController::class, 'createSubscription'])
        ->name('subscription.create');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])
        ->name('subscriptions.index');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'cancel'])
        ->name('subscriptions.cancel');
});

Route::post('stripe/webhook', [SubscriptionController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->middleware(['stripe-webhook']);

require __DIR__.'/auth.php';
