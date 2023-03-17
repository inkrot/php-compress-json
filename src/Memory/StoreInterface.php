<?php

namespace CompressJson\Memory;

use CompressJson\Core\Value;

interface StoreInterface
{
    function add(Value $value): void;

    // Type: (value: Value) => void | 'break'
    function forEach(callable $callback): void;

    /**
     * @return Value[]
     */
    function toArray(): array;
}