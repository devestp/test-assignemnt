<?php

namespace App\Models;

use Core\Foundation\Auth\User as Authenticatable;
use Database\Factories\AdminFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read string $email
 * @property-read string $password
 */
class Admin extends Authenticatable implements FilamentUser, HasName
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

    public function canAccessPanel(Panel $panel): bool
    {
        // Let all admins access the panel
        return true;
    }

    public function getFilamentName(): string
    {
        return $this->email;
    }
}
