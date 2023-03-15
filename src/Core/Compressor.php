<?php

namespace CompressJson\Core;

use CompressJson\DataTypes\JsonObject;
use CompressJson\Exception\UnsupportedDataTypeException;
use CompressJson\Memory\AbstractMemory;
use CompressJson\Memory\InMemoryMemory;

class Compressor
{
    private AbstractMemory $memory;
    private Compressed $compressed;

    private function __construct(AbstractMemory $memory)
    {
        $this->memory = $memory;
    }

    static function create(): self
    {
        return new self(
            InMemoryMemory::create()
        );
    }

    static function createWithMemory(AbstractMemory $memory): self
    {
        return new self($memory);
    }

    /**
     * @param string|array|bool|int|null|float $data
     * @throws UnsupportedDataTypeException
     */
    function compress(string|array|bool|int|null|float $data): Compressed
    {
        $root = $this->memory->addValue($data, null);
        $values = $this->memory->memToStringValues();
        return new Compressed($values, $root);
    }

    /**
     * @return string|array|bool|int|null|float
     * @throws UnsupportedDataTypeException
     */
    function decompress(Compressed $compressed): string|array|bool|int|null|float
    {
        $values = array_map(
            fn($el) => new ValueType($el),
            $compressed->getValues()
        );
        $root = $compressed->getKey();
        return JsonObject::decode($values, $root);
    }

    /**
     * @return string|array|bool|int|null|float
     * @throws UnsupportedDataTypeException
     */
    function decompressJson(
        string $compressedJson, int $depth = 512, int $flags = 0
    ): string|array|bool|int|null|float
    {
        $compressed = json_decode(
            $compressedJson, true, $depth, $flags
        );
        return $this->decompress(
            new Compressed(
                $compressed[0],
                $compressed[1],
            )
        );
    }
}