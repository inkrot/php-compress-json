<?php

namespace CompressJson\Memory;

use CompressJson\Core\ValueType;

interface StoreInterface
{
    function add(ValueType $value): void;

    // Type: (value: Value) => void | 'break'
    function forEach(callable $callback): void;

    /**
     * @return ValueType[]
     */
    function toArray(): array;
}