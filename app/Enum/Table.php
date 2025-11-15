<?php

namespace App\Enum;

/**
 * Represents the tables in the application.
 */
enum Table: string
{
    case ORDERS = 'orders';
    case USERS = 'users';
    case ORDER_BOOK_SNAPSHOTS = 'order_book_snapshots';
    case ADMINS = 'admins';
}
