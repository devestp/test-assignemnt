<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrdersStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(
                'Total Orders Count',
                fn () => Order::query()->count(),
            ),

            Stat::make(
                'Total Completed Orders Count',
                fn () => Order::query()->completed()->count(),
            ),

            Stat::make(
                'Total Pending Orders Count',
                fn () => Order::query()->pending()->count(),
            ),
        ];
    }
}
