<?php

namespace CompressJson\DataTypes;

use CompressJson\Core\Utils;

class JsonNumber
{
    const S_TO_I = [
        "0" => 0, "1" => 1, "2" => 2, "3" => 3, "4" => 4, "5" => 5, "6" => 6, "7" => 7, "8" => 8, "9" => 9,
        "A" => 10, "B" => 11, "C" => 12, "D" => 13, "E" => 14, "F" => 15, "G" => 16, "H" => 17, "I" => 18, "J" => 19,
        "K" => 20, "L" => 21, "M" => 22, "N" => 23, "O" => 24, "P" => 25, "Q" => 26, "R" => 27, "S" => 28, "T" => 29,
        "U" => 30, "V" => 31, "W" => 32, "X" => 33, "Y" => 34, "Z" => 35, "a" => 36, "b" => 37, "c" => 38, "d" => 39,
        "e" => 40, "f" => 41, "g" => 42, "h" => 43, "i" => 44, "j" => 45, "k" => 46, "l" => 47, "m" => 48, "n" => 49,
        "o" => 50, "p" => 51, "q" => 52, "r" => 53, "s" => 54, "t" => 55, "u" => 56, "v" => 57, "w" => 58, "x" => 59,
        "y" => 60, "z" => 61
    ];
    const I_TO_S = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const CONVERSIONS_COUNT = 62;

    // Special value encodings (v3.2.0)
    const ENCODE_INFINITY = 'N|+';
    const ENCODE_NEG_INFINITY = 'N|-';
    const ENCODE_NAN = 'N|0';

    /**
     * Check if value is a special float (INF, -INF, NAN)
     */
    public static function isSpecialFloat(mixed $value): bool
    {
        if (!is_float($value)) {
            return false;
        }
        return is_nan($value) || is_infinite($value);
    }

    // encodeNum
    public static function encodeNumber(mixed $num): string
    {
        // Handle special values (v3.2.0)
        if (is_float($num)) {
            if ($num === INF) {
                return self::ENCODE_INFINITY;
            }
            if ($num === -INF) {
                return self::ENCODE_NEG_INFINITY;
            }
            if (is_nan($num)) {
                return self::ENCODE_NAN;
            }
        }
        return 'n|' . self::num_to_s($num);
    }

    // decodeNum
    public static function decodeNumber(string $s): int|float
    {
        // Handle special values (v3.2.0)
        if ($s === self::ENCODE_INFINITY) {
            return INF;
        }
        if ($s === self::ENCODE_NEG_INFINITY) {
            return -INF;
        }
        if ($s === self::ENCODE_NAN) {
            return NAN;
        }

        $s = str_replace('n|', '', $s);
        return self::s_to_num($s);
    }

    // s_to_int
    public static function s_to_int(string $s): int
    {
        $acc = 0;
        $pow = 1;
        for ($i = strlen($s) - 1; $i >= 0; $i--) {
            $c = $s[$i];
            $x = self::S_TO_I[$c];
            $x *= $pow;
            $acc += $x;
            $pow *= self::CONVERSIONS_COUNT;
        }
        return $acc;
    }

    // TODO return bigint
    // s_to_big_int
    public static function s_to_big_int(string $s): int
    {
        // TODO реализовать BigInt
        $acc = 0; // TODO $acc = BigInt(0);
        $pow = 1; // TODO $pow = BigInt(1);
        $n = self::CONVERSIONS_COUNT; // TODO $n = BigInt(self::CONVERSIONS_COUNT);
        for ($i = strlen($s) - 1; $i >= 0; $i--) {
            $c = $s[$i];
            $x = self::S_TO_I[$c]; // TODO $x = BigInt(self::S_TO_I[$c]);
            $x *= $pow;
            $acc += $x;
            $pow *= $n;
        }
        return $acc;
    }

    // int_to_s
    public static function int_to_s(int $number): string
    {
        if ($number === 0) {
            return self::I_TO_S[0];
        }
        /** @var string[] $acc */
        $acc = [];
        while ($number !== 0) {
            $i = $number % self::CONVERSIONS_COUNT;
            $c = self::I_TO_S[$i];
            $acc[] = $c;
            $number -= $i;
            $number /= self::CONVERSIONS_COUNT;
        }
        return implode('', array_reverse($acc));
    }

    // TODO $bigint argument bigint type
    // big_int_to_s
    public static function big_int_to_s(int $bigint): string
    {
        // TODO реализовать BigInt
        $zero = 0; // TODO $zero = BigInt(0);
        $n = self::CONVERSIONS_COUNT; // TODO $n = BigInt(self::CONVERSIONS_COUNT);
        if ($bigint === $zero) {
            return self::I_TO_S[0];
        }
        /** @var string[] $acc */
        $acc = [];
        while ($bigint !== $zero) {
            /** @var int $i */
            $i = $bigint % $n;
            $c = self::I_TO_S[$i];
            $acc[] = $c;
            $bigint -= $i;
            $bigint /= $n;
        }
        return implode('', array_reverse($acc));
    }

    /**
     * @param $num int|double
     * @return string
     */
    // num_to_s
    public static function num_to_s(mixed $num): string
    {
        if ($num < 0) {
            return '-' . self::num_to_s(-$num);
        }
        [$a, $b] = Utils::explodeStrToRequiredPositions('.', (string) $num, 2);
        if (!$b) {
            return self::int_to_s($num);
        }
        else {
            [$b, $c] = Utils::explodeStrToRequiredPositions('e', $b, 2);
        }
        $a = self::int_str_to_s($a);
        $b = Utils::reverseStr($b);
        $b = self::int_str_to_s($b);
        $str = $a . '.' . $b;
        if ($c) {
            $str .= '.';
            switch ($c[0]) {
                case '+':
                    $c = substr($c, 1);
                    break;
                case '-':
                    $str .= '-';
                    $c = substr($c, 1);
                    break;
            }
            $c = Utils::reverseStr($c);
            $c = self::int_str_to_s($c);
            $str .= $c;
        }
        return $str;
    }

    // int_str_to_s
    public static function int_str_to_s(string $intStr): string
    {
        $num = (int)$intStr;
        if ((string)$num === $intStr) {
            return self::int_to_s($num);
        }
        return ':' . self::big_int_to_s($intStr); // TODO return ':' . self::big_int_to_s(BigInt($intStr));
    }

    // s_to_int_str
    public static function s_to_int_str(string $s): string
    {
        if ($s[0] === ':') {
            return (string)self::s_to_big_int(substr($s, 1));
        }
        return (string)self::s_to_int($s);
    }

    // s_to_num
    public static function s_to_num(string $s): int|float
    {
        if ($s[0] === '-') {
            return -self::s_to_num(substr($s, 1));
        }
        [$a, $b, $c] = Utils::explodeStrToRequiredPositions('.', $s, 3);
        if (!$b) {
            return self::s_to_int($a);
        }
        $a = self::s_to_int_str($a);
        $b = self::s_to_int_str($b);
        $b = Utils::reverseStr($b);
        $str = $a . '.' . $b;
        if ($c) {
            $str .= 'e';
            $neg = false;
            if ($c[0] === '-') {
                $neg = true;
                $c = substr($s, 1);
            }
            $c = self::s_to_int_str($c);
            $c = Utils::reverseStr($c);
            $str .= $neg ? -$c : +$c;
        }
        return +$str;
    }

}