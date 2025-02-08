<?php

namespace App\Policies;

use App\Models\Band;
use App\Models\Song;
use App\Models\User;

class SongPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Band $band): bool
    {
        return $band->isAdmin($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Song $song): bool
    {
        return $song->band->isAdmin($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Song $song): bool
    {
        return $song->band->isAdmin($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Song $song): bool
    {
        return $song->band->hasMember($user);
    }
} 