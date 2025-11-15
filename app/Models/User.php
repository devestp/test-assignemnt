<?php

namespace App\Models;

use Core\Foundation\Auth\User as Authenticatable;
use Database\Factories\UserFactory;
use Domain\Entities\User as UserEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read string $email
 * @property-read float $credit
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const EMAIL = 'email';

    public const CREDIT = 'credit';

    protected $fillable = [
        self::EMAIL,
        self::CREDIT,
    ];

    public function toEntity(): UserEntity
    {
        return new UserEntity(
            id: $this->getKey(),
            email: $this->email,
            credit: $this->credit,
        );
    }
}
