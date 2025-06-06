<?php

use App\Http\Controllers\MusicXmlViewer;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SetlistController;
use App\Http\Controllers\BandMemberController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Http\Controllers\LandingController;
use App\Http\Middleware\EnforceTrialLimits;
use App\Http\Middleware\EnsureProfileIsComplete;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportSetlistToPdf;
use App\Http\Controllers\PublicSetlistController;
use Illuminate\Http\Request;
use App\Services\GeolocationService;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

if (env('APP_ENV') !== 'production') {
    Route::get('/test', function (Request $request) {

    });
}

Route::middleware(['web', 'detect.geolocation'])->group(function () {
    Route::get('/', [LandingController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified', EnsureProfileIsComplete::class])
        ->name('dashboard');
});

Route::middleware(['auth', EnsureProfileIsComplete::class])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Subscription routes
    Route::middleware(['auth', 'verified'])->group(function () {
        // Subscription management
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])
            ->name('subscription.index');

        Route::get('/subscription/expired', [SubscriptionController::class, 'expired'])
            ->name('subscription.expired');

        Route::get('/subscription/checkout/{band}', [SubscriptionController::class, 'checkout'])
            ->name('subscription.checkout');

        Route::post('/subscription/create', [SubscriptionController::class, 'createSubscription'])
            ->name('subscription.create');

        Route::post('/subscription/update-payment', [SubscriptionController::class, 'updatePaymentMethod'])
            ->name('subscription.update-payment');

        Route::delete('/subscription/{band}/cancel', [SubscriptionController::class, 'cancel'])
            ->name('subscription.cancel');
    });

    // Band routes with access control
    Route::middleware(['band.access'])->group(function () {
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

        Route::get('/music-xml-viewer/{songFile}', MusicXmlViewer::class)->name('songs.music-xml-viewer');

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
        Route::get('/bands/{band}/setlists/{setlist}/export', ExportSetlistToPdf::class)->name('setlists.export');
        Route::get('/bands/{band}/setlists/download/{filename}', [ExportSetlistToPdf::class, 'download'])->name('setlists.download');
        Route::post('/bands/{band}/setlists/{setlist}/make-public', [SetlistController::class, 'makePublic'])->name('setlists.make-public');
        Route::post('/bands/{band}/setlists/{setlist}/make-private', [SetlistController::class, 'makePrivate'])->name('setlists.make-private');

        // Band Member Management
        Route::get('/bands/{band}/members', [BandMemberController::class, 'index'])->name('bands.members.index');
        Route::post('/bands/{band}/members/invite', [BandMemberController::class, 'invite'])->name('bands.members.invite');
        Route::delete('/bands/{band}/members/{member}', [BandMemberController::class, 'remove'])->name('bands.members.remove');
        Route::delete('/bands/{band}/invitations/{invitation}', [BandMemberController::class, 'cancelInvitation'])->name('bands.members.cancel-invitation');
    });

    // Profile setup routes
    Route::get('/profile/complete', [ProfileSetupController::class, 'show'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileSetupController::class, 'update'])->name('profile.complete.update');

    Route::post('/spotify/playlist-tracks', [SpotifyController::class, 'getPlaylistTracks'])
        ->name('spotify.playlist-tracks');
});

Route::middleware([
    'auth', EnforceTrialLimits::class
])->group(function () {
    // Group all routes that need trial limits
    Route::post('/bands/{band}/songs', [SongController::class, 'store'])->name('songs.store');
    Route::post('/bands/{band}/setlists', [SetlistController::class, 'store'])->name('setlists.store');
    // ... other routes that need trial limits ...
});

// Public route for accepting invitations
Route::get('/invitations/{token}', [BandMemberController::class, 'acceptInvitation'])->name('invitations.accept');

// Public routes for setlist access
Route::get('/setlist/{slug}', [PublicSetlistController::class, 'show'])->name('public.setlist.show');
Route::post('/setlist/{slug}/submit', [PublicSetlistController::class, 'submit'])->name('public.setlist.submit');

// Stripe webhook
Route::post('/stripe/webhook', StripeWebhookController::class)->name('cashier.webhook');

// Blog routes for public blog section
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
require __DIR__.'/auth.php';
