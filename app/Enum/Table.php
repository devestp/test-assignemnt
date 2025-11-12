<?php

namespace App\Enum;

/**
 * Represents the tables in the application.
 */
enum Table: string
{
    case ORDERS = 'orders';
    case USERS = 'users';
}
