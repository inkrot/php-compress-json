<?php

namespace CompressJson\Memory;

use CompressJson\Core\ValueType;

class InMemoryCache implements CacheInterface
{
    private array $valueMem = [];
    private array $schemaMem = [];

    function hasValue(string $key): bool
    {
        return array_key_exists($key, $this->valueMem);
    }

    function hasSchema(string $key): bool
    {
        return array_key_exists($key, $this->schemaMem);
    }

    function getValue(string $key): ?ValueType
    {
        return new ValueType($this->valueMem[$key]);
    }

    function getSchema(string $key): ?ValueType
    {
        return new ValueType($this->schemaMem[$key]);
    }

    function setValue(string $key, ?ValueType $value): void
    {
        $this->valueMem[$key] = $value;
    }

    function setSchema(string $key, ?ValueType $value): void
    {
        $this->schemaMem[$key] = $value;
    }

    function forEachValue(callable $callback): void
    {
        foreach ($this->valueMem as $key => $value) {
            if ($callback($key, $value) === 'break') {
                return;
            }
        }
    }

    function forEachSchema(callable $callback): void
    {
        foreach ($this->schemaMem as $key => $value) {
            if ($callback($key, $value) === 'break') {
                return;
            }
        }
    }
}