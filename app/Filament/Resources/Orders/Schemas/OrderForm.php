<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(Order::USER_ID)
                    ->required()
                    ->numeric(),

                TextInput::make(Order::AMOUNT)
                    ->required()
                    ->numeric(),

                TextInput::make(Order::PRICE)
                    ->required()
                    ->numeric()
                    ->prefix('$'),

                Select::make(Order::TYPE)
                    ->options(OrderType::class)
                    ->required(),

                Select::make(Order::STATE)
                    ->options(OrderState::class)
                    ->required(),

                TextInput::make(Order::MATCHED_ORDER_ID)
                    ->numeric(),
            ]);
    }
}
