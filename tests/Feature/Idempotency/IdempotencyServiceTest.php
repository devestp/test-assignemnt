<?php

namespace Tests\Feature\Idempotency;

use Core\Idempotency\Contracts\ChecksIdempotency;
use Core\Idempotency\Services\IdempotencyService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('idempotency')]
class IdempotencyServiceTest extends TestCase
{
    /**
     * Should be public to be accessible by the anonymous classes.
     */
    public const IDEMPOTENCY_TOKEN = '123456';

    public function test_it_instantiates_the_provider_if_string_is_given()
    {
        $provider = $this->createValidProvider();

        $result = $this->runValidation($provider::class, self::IDEMPOTENCY_TOKEN);

        $this->assertTrue($result);
    }

    public function test_it_throws_exception_if_provider_does_not_implement_the_correct_interface()
    {
        $provider = $this->createInvalidProvider();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provider must implement '.ChecksIdempotency::class);

        $this->runValidation($provider::class, self::IDEMPOTENCY_TOKEN);
    }

    public function test_it_returns_true_if_it_is_idempotent()
    {
        $provider = $this->createValidProvider();

        $result = $this->runValidation($provider, self::IDEMPOTENCY_TOKEN);

        $this->assertTrue($result);
    }

    public function test_it_returns_false_if_it_is_not_idempotent()
    {
        $provider = $this->createValidProvider();

        $result = $this->runValidation($provider, 'wrong!');

        $this->assertFalse($result);
    }

    private function createValidProvider(): ChecksIdempotency
    {
        return new class implements ChecksIdempotency
        {
            public function isIdempotent(string $token): bool
            {
                return $token === IdempotencyServiceTest::IDEMPOTENCY_TOKEN;
            }
        };
    }

    private function createInvalidProvider(): object
    {
        return new class {};
    }

    private function runValidation(string|ChecksIdempotency $provider, string $value): bool
    {
        $idempotency = new IdempotencyService;

        return $idempotency->check($provider, $value);
    }
}
