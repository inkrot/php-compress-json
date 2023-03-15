<?php

namespace CompressJson\Memory;

use CompressJson\Core\ValueType;

interface CacheInterface
{
    function hasValue(string $key): bool;
    function hasSchema(string $key): bool;

    function getValue(string $key): ?ValueType;
    function getSchema(string $key): ?ValueType;

    function setValue(string $key, ?ValueType $value): void;
    function setSchema(string $key, ?ValueType $value): void;

    // Type: (key: Key, value: any) => void | 'break'
    function forEachValue(callable $callback): void;
    // Type: (key: Key, value: any) => void | 'break'
    function forEachSchema(callable $callback): void;
}