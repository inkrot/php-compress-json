<?php

namespace CompressJson\Core;

class Compressed
{
    /**
     * @var string[]
     */
    private array $values;

    private string $key;

    /**
     * @param string[] $values
     * @param string $key
     */
    public function __construct(array $values, string $key)
    {
        $this->values = $values;
        $this->key = $key;
    }

    /**
     * @return string[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param string[] $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    function toArray(): array
    {
        $values = $this->getValues();
        $root = $this->getKey();
        return [$values, $root];
    }

    function toJson(int $flags = 0, int $depth = 512): string
    {
        return json_encode(
            $this->toArray(), $flags, $depth
        );
    }
}