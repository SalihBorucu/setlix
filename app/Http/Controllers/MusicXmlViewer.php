<?php

namespace App\Http\Controllers;

use App\Models\SongFile;
use App\Services\SongFileService;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MusicXmlViewer extends Controller
{
    public function __construct(SongFileService $fileService)
    {
        $this->middleware(['auth']);
    }

    public function __invoke(SongFile $songFile): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        if (!auth()->user()->isMemberOf($songFile->song->band_id)) {
            return redirect()->route('dashboard');
        }

        $music = Storage::disk('s3')->get($songFile->file_path);

        return Inertia::render('MusicXmlViewer', [
            'music' => $music
        ]);
    }
}
