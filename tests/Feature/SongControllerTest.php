<?php

namespace Tests\Feature;

use App\Models\Band;
use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SongControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;
    private Band $band;
    private Song $song;

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
        
        // Create a test song
        $this->song = Song::factory()->create([
            'band_id' => $this->band->id
        ]);
    }

    public function test_member_can_view_songs_index(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->get(route('songs.index', $this->band));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Songs/Index')
            ->has('songs')
            ->has('band')
            ->where('isAdmin', false)
        );
    }

    public function test_admin_can_create_song(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this
            ->actingAs($this->admin)
            ->post(route('songs.store', $this->band), [
                'band_id' => $this->band->id,
                'name' => 'Test Song',
                'duration_seconds' => 180,
                'notes' => 'Test notes',
                'url' => 'https://example.com',
                'document' => $file
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('songs', [
            'band_id' => $this->band->id,
            'name' => 'Test Song',
            'duration_seconds' => 180,
            'notes' => 'Test notes',
            'url' => 'https://example.com',
        ]);
        
        $song = Song::latest('id')->first();
        $this->assertNotNull($song->document_path);
        Storage::disk('local')->assertExists($song->document_path);
    }

    public function test_member_cannot_create_song(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->post(route('songs.store', $this->band), [
                'band_id' => $this->band->id,
                'name' => 'Test Song',
                'duration_seconds' => 180
            ]);

        $response->assertForbidden();
    }

    public function test_member_can_view_song(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->get(route('songs.show', [$this->band, $this->song]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Songs/Show')
            ->has('song')
            ->has('band')
            ->where('isAdmin', false)
        );
    }

    public function test_admin_can_update_song(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(route('songs.update', [$this->band, $this->song]), [
                'band_id' => $this->band->id,
                'name' => 'Updated Song Name',
                'duration_seconds' => 240
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('songs', [
            'id' => $this->song->id,
            'name' => 'Updated Song Name',
            'duration_seconds' => 240
        ]);
    }

    public function test_member_cannot_update_song(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->patch(route('songs.update', [$this->band, $this->song]), [
                'band_id' => $this->band->id,
                'name' => 'Updated Song Name',
                'duration_seconds' => 240
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_song(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->delete(route('songs.destroy', [$this->band, $this->song]));

        $response->assertRedirect();
        $this->assertSoftDeleted($this->song);
    }

    public function test_member_cannot_delete_song(): void
    {
        $response = $this
            ->actingAs($this->member)
            ->delete(route('songs.destroy', [$this->band, $this->song]));

        $response->assertForbidden();
    }

    public function test_member_can_download_document(): void
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.pdf', 100);
        $path = $file->store('documents');
        
        $this->song->update([
            'document_path' => $path,
            'document_type' => 'pdf'
        ]);

        $response = $this
            ->actingAs($this->member)
            ->get(route('songs.document', [$this->band, $this->song]));

        $response->assertStatus(200);
    }
} 