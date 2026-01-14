<?php

namespace CompressJson\Core;

use CompressJson\DataTypes\JsonArray;
use CompressJson\DataTypes\JsonBoolean;
use CompressJson\DataTypes\JsonNumber;
use CompressJson\DataTypes\JsonObject;
use CompressJson\DataTypes\JsonString;
use CompressJson\Exception\UnsupportedDataTypeException;

class Value
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

    public function getStringPrefix(): string
    {
        $str = $this->value;
        if (strlen($str) < 2) {
            return '';
        }
        return $str[0] . $str[1];
    }

    public function decode($values): mixed
    {
        if ($this->value === null) {
            return null;
        }
        //$value = (string) $value;
        if ($this->isNumeric()) {
            return $this;
        }
        else if ($this->isString()) {
            $prefix = $this->getStringPrefix();
            switch ($prefix) {
                case 'b|':
                    return JsonBoolean::decodeBoolean($this);
                case 'o|':
                    return JsonObject::decodeObject($values, $this);
                case 'n|':
                case 'N|': // Special values (v3.2.0): INF, -INF, NAN
                    return JsonNumber::decodeNumber($this);
                case 'a|':
                    return JsonArray::decodeArray($values, $this);
                default:
                    return JsonString::decodeString($this);
            }
        }
        throw new UnsupportedDataTypeException($this);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}