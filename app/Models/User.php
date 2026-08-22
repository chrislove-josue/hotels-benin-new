<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotificationFr;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }

public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function isHote(): bool
    {
        return $this->role === 'hote';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVoyageur(): bool
    {
        return $this->role === 'voyageur';
    }

    /**
     * Le compte a-t-il été approuvé par un administrateur ?
     */
    public function isApproved(): bool
    {
        return (bool) $this->is_approved;
    }

    /**
     * Le compte est-il en attente d'approbation par un administrateur ?
     */
    public function isPending(): bool
    {
        return ! $this->isApproved();
    }

    /**
     * E-mail de réinitialisation de mot de passe en français.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotificationFr($token));
    }
}
