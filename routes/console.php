<?php

use App\Console\Commands\CalculateOrderBookCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CalculateOrderBookCommand::class)
    ->everyMinute();
