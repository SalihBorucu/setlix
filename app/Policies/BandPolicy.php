<?php

namespace App\Policies;

use App\Models\Band;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BandPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view their bands
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Band $band): bool
    {
        return $band->hasMember($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create a band
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Band $band): bool
    {
        return $band->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Band $band): bool
    {
        return $band->isAdmin($user);
    }

    /**
     * Determine whether the user can manage members.
     */
    public function manageMembers(User $user, Band $band): bool
    {
        return $band->isAdmin($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Band $band): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Band $band): bool
    {
        return false;
    }
}
