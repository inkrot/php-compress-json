<?php

namespace CompressJson\DataTypes;

class JsonBoolean
{
    // encodeBool
    public static function encodeBoolean(bool $b): string
    {
        // return 'b|' + bool_to_s(b)
        return $b ? 'b|T' : 'b|F';
    }

    // decodeBool
    public static function decodeBoolean(string $str): bool
    {
        switch ($str) {
            case 'b|T':
                return true;
            case 'b|F':
                return false;
        }
        return !!$str;
    }
}