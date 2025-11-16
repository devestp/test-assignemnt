<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Domain\Enum\OrderState;
use Domain\Enum\OrderType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Order::USER_ID)
                    ->numeric()
                    ->sortable(),

                TextColumn::make(Order::AMOUNT)
                    ->numeric()
                    ->sortable(),

                TextColumn::make(Order::PRICE)
                    ->money()
                    ->sortable(),

                TextColumn::make(Order::TYPE)
                    ->badge()
                    ->searchable(),

                TextColumn::make(Order::STATE)
                    ->badge()
                    ->searchable(),

                TextColumn::make(Order::MATCHED_ORDER_ID)
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make(Order::STATE)
                    ->options([
                        OrderState::PENDING->value => OrderState::PENDING->value,
                        OrderState::COMPLETED->value => OrderState::COMPLETED->value,
                    ])->attribute(Order::STATE),

                SelectFilter::make(Order::TYPE)
                    ->options([
                        OrderType::SELL->value => OrderType::SELL->value,
                        OrderType::BUY->value => OrderType::BUY->value,
                    ])->attribute(Order::TYPE),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
