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

    public function isNumeric(): bool
    {
        return is_numeric((string)$this->value);
    }

    public function isString(): bool
    {
        return is_string($this->value);
    }

    public function getStringPrefix(): bool
    {
        $str = $this->value;
        return $str[0] . $str[1];
    }

    public function __toString(): string
    {
        return $this->value;
    }
}