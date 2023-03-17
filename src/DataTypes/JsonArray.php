<?php

namespace CompressJson\DataTypes;

use CompressJson\Exception\UnsupportedDataTypeException;

class JsonArray
{
    /**
     * @throws UnsupportedDataTypeException
     */
    public static function decodeArray(array $values, string $s)
    {
        if ($s === 'a|') {
            return [];
        }
        $vs = explode('|', $s);
        $n = count($vs) - 1;
        $xs = [];
        for ($i = 0; $i < $n; $i++) {
            $v = $vs[$i + 1];
            $v = JsonObject::decodeValues($values, $v);
            $xs[] = $v; //$xs[$i] = $v;
        }
        return $xs;
    }

}