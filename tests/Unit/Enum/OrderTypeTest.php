<?php

namespace Tests\Unit\Enum;

use Domain\Enum\OrderType;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('enum')]
class OrderTypeTest extends TestCase
{
    public function test_is_sell_method_return_true_if_is_sell()
    {
        $this->assertTrue(
            OrderType::SELL->isSell(),
        );
    }

    public function test_is_sell_method_return_false_if_is_not_sell()
    {
        $this->assertFalse(
            OrderType::BUY->isSell(),
        );
    }

    public function test_opposite_method_return_the_opposite_type()
    {
        $this->assertEquals(
            OrderType::SELL,
            OrderType::BUY->opposite(),
        );
    }
}
