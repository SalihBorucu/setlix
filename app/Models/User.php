<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all bands that the user is a member of
     */
    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get bands where the user is an admin
     */
    public function adminBands(): BelongsToMany
    {
        return $this->bands()->wherePivot('role', 'admin');
    }

    /**
     * Check if user is admin of a specific band
     */
    public function isAdminOf(Band $band): bool
    {
        return $this->adminBands()->where('band_id', $band->id)->exists();
    }
}
