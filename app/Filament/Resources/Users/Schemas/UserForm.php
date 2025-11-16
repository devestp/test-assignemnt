<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(User::EMAIL)
                    ->label('Email address')
                    ->email()
                    ->required(),

                TextInput::make(User::CREDIT)
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
