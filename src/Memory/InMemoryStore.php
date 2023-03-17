<?php

namespace CompressJson\Memory;

use CompressJson\Core\Value;

class InMemoryStore implements StoreInterface
{
    /** @var Value[] */
    private array $mem = [];

    function add(Value $value): void
    {
        $this->mem[] = $value;
    }

    function forEach(callable $callback): void
    {
        foreach ($this->mem as $m) {
            if ($callback($m) === 'break') {
                return;
            }
        }
    }

    function toArray(): array
    {
        return $this->mem;
    }
}