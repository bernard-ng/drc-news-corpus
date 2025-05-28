<?php

declare(strict_types=1);

namespace Tests\Behat\State;

/**
 * Class SharedStorage.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class SharedStorage
{
    private array $storage = [];

    public function get(string $key): mixed
    {
        return $this->storage[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        $this->storage[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($this->storage[$key]);
        }
    }
}
