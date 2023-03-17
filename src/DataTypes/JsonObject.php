<?php

namespace CompressJson\DataTypes;

use CompressJson\Core\ValueType;
use CompressJson\Exception\UnsupportedDataTypeException;

class JsonObject
{

    private static function decodeKey(string $key): int
    {
        return is_numeric($key) ? $key : JsonNumber::s_to_int($key);
    }

    /**
     * @param ValueType[] $values
     * @throws UnsupportedDataTypeException
     */
    public static function decodeValues(array $values, string $key)
    {
        if ($key === '' || $key === '_') {
            return null;
        }
        $id = self::decodeKey($key);
        $value = $values[$id];
        if ($value === null) {
            return null;
        }
        //$value = (string) $value;
        if ($value->isNumeric()) {
            return $value;
        }
        else if ($value->isString()) {
            $prefix = $value->getStringPrefix();
            switch ($prefix) {
                case 'b|':
                    return JsonBoolean::decodeBoolean($value);
                case 'o|':
                    return JsonObject::decodeObject($values, $value);
                case 'n|':
                    return JsonNumber::decodeNumber($value);
                case 'a|':
                    return JsonArray::decodeArray($values, $value);
                default:
                    return JsonString::decodeString($value);
            }
        }
        throw new UnsupportedDataTypeException($value);
    }

    /**
     * @param ValueType[] $values
     * @param string $s
     * @return array
     * @throws UnsupportedDataTypeException
     */
    public static function decodeObject(array $values, string $s): array
    {
        if ($s === 'o|') {
            return [];
        }
        $o = [];
        $vs = explode('|', $s);
        $keyId = $vs[1];
        $keys = JsonObject::decodeValues($values, $keyId);
        $n = count($vs);
        if ($n - 2 === 1 && !is_array($keys)) {
            // single-key object using existing value as key
            $keys = [$keys];
        }
        for ($i = 2; $i < $n; $i++) {
            $k = $keys[$i - 2];
            $v = $vs[$i];
            $v = JsonObject::decodeValues($values, $v);
            $o[$k] = $v;
        }
        return $o;
    }
}