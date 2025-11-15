<?php

namespace Domain\Concerns;

use RuntimeException;

/**
 * Adds the ability to hold additional data for transmission.
 *
 * Useful in scenarios where a value object or DTO needs to allow the
 * implementation layer to pass extra info.
 */
trait HasAdditionalData
{
    private array $additional = [];

    /**
     * Adds additional data.
     *
     * You can pass either:
     * 1. An associative array of key-value pairs to add multiple items at once.
     * 2. A single key and value to add one item.
     *
     * @param  array<string, mixed>|string  $key  Either an array of data or a single key.
     * @param  mixed|null  $value  The value associated with the key (ignored if $key is an array).
     */
    public function additional(array|string $key, mixed $value = null): self
    {
        if (is_array($key)) {
            $this->additional = array_merge($this->additional, $key);
        } else {
            $this->additional[$key] = $value;
        }

        return $this;
    }

    /**
     * Retrieves additional data from the bag.
     *
     * If a key is provided, returns the value associated with that key.
     * Otherwise, returns the entire additional data array.
     *
     * @param  string|null  $key  Optional key to retrieve a specific value.
     * @return mixed The value for the given key, or the full additional data array if no key is provided.
     */
    public function getAdditional(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->additional;
        }

        return $this->additional[$key] ?? null;
    }

    /**
     * Retrieves the value for the given key from the additional data bag.
     *
     * Throws a `RuntimeException` if the key does not exist, as the absence
     * of the value is considered a bug or an unexpected state.
     *
     * @param  string  $key  The key to retrieve from the additional data bag.
     * @return mixed The value associated with the given key.
     *
     * @throws RuntimeException If the key is not found in the bag.
     */
    public function getAdditionalOrFail(string $key): mixed
    {
        return $this->additional[$key] ?? throw new RuntimeException("Additional bag does not contain key $key.");
    }
}
