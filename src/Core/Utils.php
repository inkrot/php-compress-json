<?php

namespace CompressJson\Core;

class Utils
{
    public static function isAssocArray(mixed $arr)
    {
        if (!is_array($arr)) {
            return false;
        }
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public static function reverseStr(string $s): string
    {
        $arr = array_reverse(
            str_split($s)
        );
        return implode('', $arr);
    }

    public static function explodeStrToRequiredPositions(string $separator, string $strNum, int $count): array {
        $arr = explode($separator, $strNum);
        $positionsArr = array_fill(0, $count, null);
        for ($i = 0; $i < $count; $i++) {
            if (isset($arr[$i])) {
                $positionsArr[$i] = $arr[$i];
            }
        }
        return $positionsArr;
    }

}