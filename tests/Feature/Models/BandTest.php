<?php

namespace Tests\Feature\Models;

use App\Models\Band;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BandTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_band(): void
    {
        $band = Band::factory()->create();
        
        $this->assertDatabaseHas('bands', [
            'id' => $band->id,
            'name' => $band->name,
        ]);
    }

    public function test_can_add_member_to_band(): void
    {
        $band = Band::factory()->create();
        $user = User::factory()->create();

        $band->members()->attach($user, ['role' => 'member']);

        $this->assertTrue($band->hasMember($user));
        $this->assertFalse($band->isAdmin($user));
    }

    public function test_can_add_admin_to_band(): void
    {
        $band = Band::factory()->create();
        $user = User::factory()->create();

        $band->members()->attach($user, ['role' => 'admin']);

        $this->assertTrue($band->hasMember($user));
        $this->assertTrue($band->isAdmin($user));
    }

    public function test_can_get_band_admins(): void
    {
        $band = Band::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();

        $band->members()->attach($admin, ['role' => 'admin']);
        $band->members()->attach($member, ['role' => 'member']);

        $this->assertCount(1, $band->admins);
        $this->assertEquals($admin->id, $band->admins->first()->id);
    }

    public function test_can_get_regular_members(): void
    {
        $band = Band::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();

        $band->members()->attach($admin, ['role' => 'admin']);
        $band->members()->attach($member, ['role' => 'member']);

        $this->assertCount(1, $band->regularMembers);
        $this->assertEquals($member->id, $band->regularMembers->first()->id);
    }

    public function test_user_can_be_member_of_multiple_bands(): void
    {
        $user = User::factory()->create();
        $band1 = Band::factory()->create();
        $band2 = Band::factory()->create();

        $band1->members()->attach($user, ['role' => 'member']);
        $band2->members()->attach($user, ['role' => 'admin']);

        $this->assertCount(2, $user->bands);
        $this->assertCount(1, $user->adminBands);
    }
}
