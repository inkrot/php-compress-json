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

    function toJson(int $flags = 0, int $depth = 512): string
    {
        $values = $this->getValues();
        $root = $this->getKey();
        return json_encode(
            [$values, $root], $flags, $depth
        );
    }
}