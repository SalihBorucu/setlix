<?php

namespace App\Services;

use App\Models\Song;
use App\Models\SongFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SongFileService
{
    /**
     * Maximum file size in bytes (10MB)
     */
    const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Store a new file for a song
     */
    public function store(Song $song, UploadedFile $file, string $type): SongFile
    {
        // Validate file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum limit of 10MB');
        }

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);
        
        // Store file in S3
        $path = $file->storeAs(
            "songs/{$song->id}",
            $filename,
            's3'
        );

        // Create database record
        return SongFile::create([
            'song_id' => $song->id,
            'type' => $type,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize()
        ]);
    }

    /**
     * Delete a song file
     */
    public function delete(SongFile $songFile): bool
    {
        // Delete from S3
        Storage::disk('s3')->delete($songFile->file_path);

        // Delete database record
        return $songFile->delete();
    }

    /**
     * Get temporary URL for file download
     */
    public function getTemporaryUrl(SongFile $songFile, int $minutes = 5): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $songFile->file_path,
            now()->addMinutes($minutes)
        );
    }

    /**
     * Generate a unique filename
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid() . '_' . time() . '.' . $extension;
    }
} 