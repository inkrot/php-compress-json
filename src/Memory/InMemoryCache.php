<?php

namespace CompressJson\Memory;

use CompressJson\Core\Value;

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

    function getValue(string $key): ?Value
    {
        return new Value($this->valueMem[$key]);
    }

    function getSchema(string $key): ?Value
    {
        return new Value($this->schemaMem[$key]);
    }

    function setValue(string $key, ?Value $value): void
    {
        $this->valueMem[$key] = $value;
    }

    function setSchema(string $key, ?Value $value): void
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