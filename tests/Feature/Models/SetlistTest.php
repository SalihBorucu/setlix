<?php

namespace Tests\Feature\Models;

use App\Models\Band;
use App\Models\Song;
use App\Models\Setlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetlistTest extends TestCase
{
    use RefreshDatabase;

    private Band $band;
    private array $songs;
    private Setlist $setlist;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->band = Band::factory()->create();
        
        // Create test songs
        $this->songs = Song::factory(3)->create([
            'band_id' => $this->band->id,
            'duration_seconds' => 180 // 3 minutes each
        ])->toArray();
        
        // Create a test setlist
        $this->setlist = Setlist::create([
            'band_id' => $this->band->id,
            'name' => 'Test Setlist',
            'description' => 'Test Description',
            'total_duration' => 540 // 9 minutes total
        ]);

        // Add songs to setlist
        foreach ($this->songs as $index => $song) {
            $this->setlist->songs()->attach($song['id'], [
                'order' => $index,
                'notes' => "Note for song {$index}"
            ]);
        }
    }

    public function test_can_create_setlist(): void
    {
        $setlist = Setlist::create([
            'band_id' => $this->band->id,
            'name' => 'New Setlist',
            'description' => 'New Description',
            'total_duration' => 0
        ]);
        
        $this->assertDatabaseHas('setlists', [
            'id' => $setlist->id,
            'name' => 'New Setlist',
            'band_id' => $this->band->id
        ]);
    }

    public function test_setlist_belongs_to_band(): void
    {
        $this->assertEquals($this->band->id, $this->setlist->band->id);
    }

    public function test_setlist_has_many_songs(): void
    {
        $this->assertCount(3, $this->setlist->songs);
        
        foreach ($this->setlist->songs as $index => $song) {
            $this->assertEquals($this->songs[$index]['id'], $song->id);
            $this->assertEquals($index, $song->pivot->order);
            $this->assertEquals("Note for song {$index}", $song->pivot->notes);
        }
    }

    public function test_can_format_duration(): void
    {
        $this->assertEquals('9:00', $this->setlist->formatted_duration);

        $setlist = Setlist::create([
            'band_id' => $this->band->id,
            'name' => 'Duration Test',
            'total_duration' => 3665 // 1 hour, 1 minute, 5 seconds
        ]);

        $this->assertEquals('61:05', $setlist->formatted_duration);
    }

    public function test_can_update_total_duration(): void
    {
        $this->setlist->updateTotalDuration();
        $this->assertEquals(540, $this->setlist->total_duration); // 3 songs * 180 seconds

        // Add another song
        $newSong = Song::factory()->create([
            'band_id' => $this->band->id,
            'duration_seconds' => 240 // 4 minutes
        ]);
        
        $this->setlist->songs()->attach($newSong->id, [
            'order' => 3
        ]);

        $this->setlist->updateTotalDuration();
        $this->assertEquals(780, $this->setlist->total_duration); // (3 * 180) + 240 seconds
    }

    public function test_setlist_is_soft_deleted(): void
    {
        $this->setlist->delete();

        $this->assertSoftDeleted('setlists', [
            'id' => $this->setlist->id
        ]);

        // Verify relationships are maintained
        $this->assertDatabaseHas('setlist_song', [
            'setlist_id' => $this->setlist->id
        ]);
    }

    public function test_can_store_and_retrieve_song_order(): void
    {
        $songIds = array_column($this->songs, 'id');
        
        $this->setlist->update([
            'song_order' => $songIds
        ]);

        $this->assertEquals($songIds, $this->setlist->fresh()->song_order);
    }
} 