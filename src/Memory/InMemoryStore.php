<?php

namespace CompressJson\Memory;

use CompressJson\Core\ValueType;

class InMemoryStore implements StoreInterface
{
    /** @var ValueType[] */
    private array $mem = [];

    function add(ValueType $value): void
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