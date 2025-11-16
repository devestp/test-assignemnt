<?php

namespace Tests\Unit\ValueObjects;

use Brick\Math\BigDecimal;
use Domain\ValueObjects\GroupedOrder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('value objects')]
class GroupedOrderTest extends TestCase
{
    public function test_it_creates()
    {
        $price = 1000;
        $count = 10;

        $vo = new GroupedOrder(
            price: BigDecimal::of($price),
            count: $count,
        );

        $this->assertTrue($vo->getPrice()->isEqualTo($price));
        $this->assertEquals($count, $vo->getCount());
    }
}
