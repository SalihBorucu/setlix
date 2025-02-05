<?php

namespace Tests\Feature\Models;

use App\Models\Band;
use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_song(): void
    {
        $band = Band::factory()->create();
        $song = Song::factory()->create([
            'band_id' => $band->id
        ]);
        
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'name' => $song->name,
            'band_id' => $band->id
        ]);
    }

    public function test_song_belongs_to_band(): void
    {
        $band = Band::factory()->create();
        $song = Song::factory()->create([
            'band_id' => $band->id
        ]);

        $this->assertEquals($band->id, $song->band->id);
    }

    public function test_can_format_duration(): void
    {
        $song = Song::factory()->create([
            'duration_seconds' => 185 // 3:05
        ]);

        $this->assertEquals('03:05', $song->formatted_duration);
    }

    public function test_can_handle_document(): void
    {
        Storage::fake('local');
        
        $band = Band::factory()->create();
        $song = Song::factory()->create([
            'band_id' => $band->id,
            'document_path' => 'documents/test.pdf',
            'document_type' => 'pdf'
        ]);

        $this->assertTrue($song->hasDocument());
        $this->assertNotNull($song->getDocumentUrl());

        $song->deleteDocument();

        $this->assertFalse($song->hasDocument());
        $this->assertNull($song->document_path);
        $this->assertNull($song->document_type);
    }

    public function test_song_is_soft_deleted(): void
    {
        $song = Song::factory()->create();
        $songId = $song->id;

        $song->delete();

        $this->assertSoftDeleted('songs', [
            'id' => $songId
        ]);
    }
} 