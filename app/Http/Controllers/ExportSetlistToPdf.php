<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Models\Setlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportSetlistToPdf extends Controller
{
    /**
     * Generate and store the PDF, then return the download URL
     */
    public function __invoke(Band $band, Setlist $setlist)
    {
        // Authorize the request
        $this->authorize('view', $band);

        // Load the setlist with all its items and related songs
        $setlist->load(['items.song.files']);

        // Generate PDF
        $pdf = PDF::loadView('pdf.setlist', [
            'band' => $band,
            'setlist' => $setlist,
        ]);

        // Generate a unique filename
        $filename = sprintf(
            'setlist_%s_%s.pdf',
            Str::slug($setlist->name),
            now()->format('Y-m-d_His')
        );

        // Store in S3
        $path = "setlists/{$band->id}/{$filename}";
        Storage::disk('s3')->put($path, $pdf->output());

        // Return the download URL
        return response()->json([
            'success' => true,
            'download_url' => route('setlists.download', [
                'band' => $band->id,
                'filename' => $filename
            ])
        ]);
    }

    /**
     * Download a previously generated PDF file
     */
    public function download(Band $band, $filename)
    {
        // Authorize the request
        $this->authorize('view', $band);

        // Validate filename to prevent directory traversal
        if (strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            abort(404);
        }

        $path = "setlists/{$band->id}/{$filename}";

        // Check if file exists
        if (!Storage::disk('s3')->exists($path)) {
            abort(404);
        }

        // Get file contents
        $fileContents = Storage::disk('s3')->get($path);

        // Return file for download
        return response($fileContents, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($fileContents)
        ]);
    }
} 