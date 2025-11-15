<?php

namespace App\Models;

use Core\Database\Eloquent\Model;
use Database\Factories\AdminFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read string $email
 * @property-read string $password
 */
class Admin extends Model
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory;

    public const EMAIL = 'email';

    public const PASSWORD = 'password';

    public const REMEMBER_TOKEN = 'remember_token';

    protected $fillable = [
        self::EMAIL,
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];
}
