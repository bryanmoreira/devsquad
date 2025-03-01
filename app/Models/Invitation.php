<?php

namespace App\Models;

use App\Mail\InviteUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'expires_at'   => 'datetime',
        'activated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            $invitation->code = Str::uuid();

            if (!$invitation->expires_at) {
                $invitation->expires_at = now()->addHour();
            }
        });

        static::created(
            fn (Invitation $invitation) => Mail::to($invitation->email)
                ->send(new InviteUser($invitation))
        );
    }

    public function isExpired(): bool
    {
        return $this->expires_at->lessThan(now());
    }

    public function isActivated(): bool
    {
        return !is_null($this->activated_at);
    }
}
