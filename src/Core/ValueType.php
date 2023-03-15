<?php

namespace CompressJson\Core;

class ValueType
{
    private ?string $value;

    /**
     * @param string|null $value
     */
    public function __construct(?string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}