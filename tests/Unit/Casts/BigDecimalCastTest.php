<?php

namespace Tests\Unit\Casts;

use App\Casts\BigDecimalCast;
use Brick\Math\BigDecimal;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('casts')]
class BigDecimalCastTest extends TestCase
{
    public function test_get_method_throws_exception_if_value_is_not_correct()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$value must be an instance of '.BigDecimal::class.', int, float or string. boolean given.');

        $this->runGet(true);
    }

    public function test_get_method_converts_to_order_book()
    {
        $result = $this->runGet(1);

        $this->assertTrue($result->isEqualTo(1));
    }

    public function test_set_method_throws_exception_if_value_is_not_an_order_book()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$value must be an instance of '.BigDecimal::class.', int, float or string. boolean given.');

        $this->runSet(true);
    }

    public function test_set_method_converts_to_string()
    {
        $result = $this->runSet(1);

        $this->assertEquals('1.000000', $result);
    }

    private function runGet($value): BigDecimal
    {
        $model = new class extends Model {};

        $cast = new BigDecimalCast;

        return $cast->get($model, 'key', $value, []);
    }

    private function runSet($value): string
    {
        $model = new class extends Model {};

        $cast = new BigDecimalCast;

        return $cast->set($model, 'key', $value, []);
    }
}
