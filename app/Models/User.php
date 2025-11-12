<?php

namespace App\Models;

use Core\Foundation\Auth\User as Authenticatable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @property-read string $email
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const EMAIL = 'email';

    protected $fillable = [
        self::EMAIL,
    ];
}
