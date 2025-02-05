<?php

namespace Tests\Feature;

use App\Models\Band;
use App\Models\User;
use App\Models\Song;
use App\Models\Setlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetlistControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private Band $band;
    private Setlist $setlist;
    private array $songs;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users and band
        $this->admin = User::factory()->create();
        $this->member = User::factory()->create();
        $this->band = Band::factory()->create();
        
        // Set up relationships
        $this->band->members()->attach($this->admin, ['role' => 'admin']);
        $this->band->members()->attach($this->member, ['role' => 'member']);
        
        // Create test songs
        $this->songs = Song::factory(3)->create([
            'band_id' => $this->band->id
        ])->toArray();
        
        // Create a test setlist
        $this->setlist = Setlist::create([
            'band_id' => $this->band->id,
            'name' => 'Test Setlist',
            'description' => 'Test Description',
            'total_duration' => 600
        ]);

        // Add songs to setlist
        foreach ($this->songs as $index => $song) {
            $this->setlist->songs()->attach($song['id'], [
                'order' => $index,
                'notes' => "Note for song {$index}"
            ]);
        }
    }

    public function test_member_can_view_setlists_index(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->get(route('setlists.index', $this->band));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Setlists/Index')
            ->has('setlists')
            ->has('band')
            ->where('isAdmin', false)
        );
    }

    public function test_admin_can_create_setlist(): void
    {
        $songIds = array_column($this->songs, 'id');
        
        $response = $this
            ->actingAs($this->admin)
            ->post(route('setlists.store', $this->band), [
                'band_id' => $this->band->id,
                'name' => 'New Setlist',
                'description' => 'New Description',
                'song_order' => $songIds,
                'total_duration' => 0 // This will be calculated
            ]);

        $response->assertRedirect();
        
        // Get the total duration from the songs
        $totalDuration = Song::whereIn('id', $songIds)->sum('duration_seconds');
        
        $this->assertDatabaseHas('setlists', [
            'band_id' => $this->band->id,
            'name' => 'New Setlist',
            'description' => 'New Description',
            'total_duration' => $totalDuration
        ]);

        $setlist = Setlist::latest('id')->first();
        foreach ($songIds as $index => $songId) {
            $this->assertDatabaseHas('setlist_song', [
                'setlist_id' => $setlist->id,
                'song_id' => $songId,
                'order' => $index
            ]);
        }
    }

    public function test_member_cannot_create_setlist(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->post(route('setlists.store', $this->band), [
                'band_id' => $this->band->id,
                'name' => 'New Setlist',
                'song_order' => []
            ]);

        $response->assertForbidden();
    }

    public function test_member_can_view_setlist(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->get(route('setlists.show', [$this->band, $this->setlist]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Setlists/Show')
            ->has('setlist.songs')
            ->has('band')
            ->where('isAdmin', false)
        );
    }

    public function test_admin_can_update_setlist(): void
    {
        $songIds = array_column($this->songs, 'id');
        // Reverse the song order
        $songIds = array_reverse($songIds);

        $response = $this
            ->actingAs($this->admin)
            ->patch(route('setlists.update', [$this->band, $this->setlist]), [
                'band_id' => $this->band->id,
                'name' => 'Updated Setlist',
                'description' => 'Updated Description',
                'song_order' => $songIds,
                'total_duration' => 0 // This will be calculated
            ]);

        $response->assertRedirect();
        
        // Get the total duration from the songs
        $totalDuration = Song::whereIn('id', $songIds)->sum('duration_seconds');
        
        $this->assertDatabaseHas('setlists', [
            'id' => $this->setlist->id,
            'name' => 'Updated Setlist',
            'description' => 'Updated Description',
            'total_duration' => $totalDuration
        ]);

        // Verify song order was updated
        foreach ($songIds as $index => $songId) {
            $this->assertDatabaseHas('setlist_song', [
                'setlist_id' => $this->setlist->id,
                'song_id' => $songId,
                'order' => $index
            ]);
        }
    }

    public function test_member_cannot_update_setlist(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->patch(route('setlists.update', [$this->band, $this->setlist]), [
                'band_id' => $this->band->id,
                'name' => 'Updated Setlist'
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_setlist(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->delete(route('setlists.destroy', [$this->band, $this->setlist]));

        $response->assertRedirect();
        $this->assertSoftDeleted($this->setlist);
        $this->assertDatabaseMissing('setlist_song', [
            'setlist_id' => $this->setlist->id
        ]);
    }

    public function test_member_cannot_delete_setlist(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->delete(route('setlists.destroy', [$this->band, $this->setlist]));

        $response->assertForbidden();
        $this->assertDatabaseHas('setlists', ['id' => $this->setlist->id]);
    }

    public function test_setlist_requires_valid_data(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(route('setlists.store', $this->band), [
                'band_id' => $this->band->id,
                'name' => '', // Invalid: empty name
                'song_order' => ['invalid-id'] // Invalid: non-existent song
            ]);

        $response->assertSessionHasErrors(['name', 'song_order.*']);
    }

    public function test_setlist_updates_total_duration(): void
    {
        $songs = Song::factory(2)->create([
            'band_id' => $this->band->id,
            'duration_seconds' => 300 // 5 minutes each
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(route('setlists.store', $this->band), [
                'band_id' => $this->band->id,
                'name' => 'Duration Test Setlist',
                'song_order' => $songs->pluck('id')->toArray(),
                'total_duration' => 0 // This will be calculated
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('setlists', [
            'name' => 'Duration Test Setlist',
            'total_duration' => 600 // 2 songs * 300 seconds
        ]);
    }
} 