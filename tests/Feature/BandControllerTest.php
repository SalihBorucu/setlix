<?php

namespace Tests\Feature;

use App\Models\Band;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BandControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_bands_index(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();
        $band->members()->attach($user, ['role' => 'member']);

        $response = $this
            ->actingAs($user)
            ->get(route('bands.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Bands/Index')
            ->has('bands', 1)
        );
    }

    public function test_user_can_create_band(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('bands.store'), [
                'name' => 'Test Band',
                'description' => 'Test Description',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bands', [
            'name' => 'Test Band',
            'description' => 'Test Description',
        ]);

        $band = Band::first();
        $this->assertTrue($band->isAdmin($user));
    }

    public function test_user_cannot_create_band_with_invalid_data(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('bands.store'), [
                'name' => '', // Invalid: empty name
                'description' => 'Test Description',
            ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_user_can_view_band_details(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();
        $band->members()->attach($user, ['role' => 'member']);

        $response = $this
            ->actingAs($user)
            ->get(route('bands.show', $band));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Bands/Show')
            ->has('band')
            ->has('isAdmin')
        );
    }

    public function test_non_member_cannot_view_band_details(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('bands.show', $band));

        $response->assertForbidden();
    }

    public function test_admin_can_update_band(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();
        $band->members()->attach($user, ['role' => 'admin']);

        $response = $this
            ->actingAs($user)
            ->patch(route('bands.update', $band), [
                'name' => 'Updated Band Name',
                'description' => 'Updated Description',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bands', [
            'id' => $band->id,
            'name' => 'Updated Band Name',
            'description' => 'Updated Description',
        ]);
    }

    public function test_non_admin_cannot_update_band(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();
        $band->members()->attach($user, ['role' => 'member']);

        $response = $this
            ->actingAs($user)
            ->patch(route('bands.update', $band), [
                'name' => 'Updated Band Name',
                'description' => 'Updated Description',
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_delete_band(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();
        $band->members()->attach($user, ['role' => 'admin']);

        $response = $this
            ->actingAs($user)
            ->delete(route('bands.destroy', $band));

        $response->assertRedirect(route('bands.index'));
        $this->assertSoftDeleted($band);
    }

    public function test_non_admin_cannot_delete_band(): void
    {
        $user = User::factory()->create();
        $band = Band::factory()->create();
        $band->members()->attach($user, ['role' => 'member']);

        $response = $this
            ->actingAs($user)
            ->delete(route('bands.destroy', $band));

        $response->assertForbidden();
        $this->assertDatabaseHas('bands', ['id' => $band->id]);
    }
}
