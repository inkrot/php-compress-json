<?php

namespace CompressJson\DataTypes;

class JsonString
{
    // encodeStr
    public static function encodeString(string $str): string
    {
        if (strlen($str) < 2) {
            return $str;
        }
        $prefix = $str[0] . $str[1];
        switch ($prefix) {
            case 'b|':
            case 'o|':
            case 'n|':
            case 'a|':
            case 's|':
                $str = 's|' . $str;
        }
        return $str;
    }

    // decodeStr
    public static function decodeString(string $str): string
    {
        if (strlen($str) < 2) {
            return $str;
        }
        $prefix = $str[0] . $str[1];
        return $prefix === 's|' ? substr($str, 2) : $str;
    }
}