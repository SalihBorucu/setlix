<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BandController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\SetlistController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'bands' => Auth::user()->bands()->with('members')->get()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Band routes
    Route::resource('bands', BandController::class);
    
    // Song routes (nested under bands)
    Route::get('/bands/{band}/songs', [SongController::class, 'index'])->name('songs.index');
    Route::get('/bands/{band}/songs/create', [SongController::class, 'create'])->name('songs.create');
    Route::post('/bands/{band}/songs', [SongController::class, 'store'])->name('songs.store');
    Route::get('/bands/{band}/songs/{song}', [SongController::class, 'show'])->name('songs.show');
    Route::get('/bands/{band}/songs/{song}/edit', [SongController::class, 'edit'])->name('songs.edit');
    Route::patch('/bands/{band}/songs/{song}', [SongController::class, 'update'])->name('songs.update');
    Route::delete('/bands/{band}/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
    Route::get('/bands/{band}/songs/{song}/document', [SongController::class, 'downloadDocument'])->name('songs.document');

    // Setlist routes (nested under bands)
    Route::get('/bands/{band}/setlists', [SetlistController::class, 'index'])->name('setlists.index');
    Route::get('/bands/{band}/setlists/create', [SetlistController::class, 'create'])->name('setlists.create');
    Route::post('/bands/{band}/setlists', [SetlistController::class, 'store'])->name('setlists.store');
    Route::get('/bands/{band}/setlists/{setlist}', [SetlistController::class, 'show'])->name('setlists.show');
    Route::get('/bands/{band}/setlists/{setlist}/edit', [SetlistController::class, 'edit'])->name('setlists.edit');
    Route::patch('/bands/{band}/setlists/{setlist}', [SetlistController::class, 'update'])->name('setlists.update');
    Route::delete('/bands/{band}/setlists/{setlist}', [SetlistController::class, 'destroy'])->name('setlists.destroy');
});

require __DIR__.'/auth.php';
