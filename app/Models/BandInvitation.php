<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BandInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'band_id',
        'email',
        'role',
        'token',
        'expires_at',
        'accepted_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            $invitation->token = Str::random(32);
            $invitation->expires_at = now()->addDays(7); // Invitations expire after 7 days
        });
    }

    public function band()
    {
        return $this->belongsTo(Band::class);
    }
}
