<?php

namespace CompressJson\Memory;

use CompressJson\Core\Value;

interface CacheInterface
{
    function hasValue(string $key): bool;
    function hasSchema(string $key): bool;

    function getValue(string $key): ?Value;
    function getSchema(string $key): ?Value;

    function setValue(string $key, ?Value $value): void;
    function setSchema(string $key, ?Value $value): void;

    // Type: (key: Key, value: any) => void | 'break'
    function forEachValue(callable $callback): void;
    // Type: (key: Key, value: any) => void | 'break'
    function forEachSchema(callable $callback): void;
}