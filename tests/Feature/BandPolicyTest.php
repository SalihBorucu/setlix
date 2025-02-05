<?php

namespace Tests\Feature;

use App\Models\Band;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BandPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Band $band;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->band = Band::factory()->create();
    }

    public function test_any_user_can_view_bands_list(): void
    {
        $this->assertTrue($this->user->can('viewAny', Band::class));
    }

    public function test_any_user_can_create_band(): void
    {
        $this->assertTrue($this->user->can('create', Band::class));
    }

    public function test_member_can_view_band(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'member']);
        
        $this->assertTrue($this->user->can('view', $this->band));
    }

    public function test_non_member_cannot_view_band(): void
    {
        $this->assertFalse($this->user->can('view', $this->band));
    }

    public function test_admin_can_update_band(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'admin']);
        
        $this->assertTrue($this->user->can('update', $this->band));
    }

    public function test_member_cannot_update_band(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'member']);
        
        $this->assertFalse($this->user->can('update', $this->band));
    }

    public function test_admin_can_delete_band(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'admin']);
        
        $this->assertTrue($this->user->can('delete', $this->band));
    }

    public function test_member_cannot_delete_band(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'member']);
        
        $this->assertFalse($this->user->can('delete', $this->band));
    }

    public function test_admin_can_manage_members(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'admin']);
        
        $this->assertTrue($this->user->can('manageMembers', $this->band));
    }

    public function test_member_cannot_manage_members(): void
    {
        $this->band->members()->attach($this->user, ['role' => 'member']);
        
        $this->assertFalse($this->user->can('manageMembers', $this->band));
    }
}
