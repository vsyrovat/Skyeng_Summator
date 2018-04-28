<?php declare(strict_types=1);

class Summator
{
    private static function addInt(string $num1, string $num2, bool $t = false): string
    {
        $num1 = strrev($num1);
        $num2 = strrev($num2);
        $l = strlen($num1);

        $result = '';

        for ($i = 0; $i < $l; $i++) {
            $s = intval(substr($num1, $i, 1)) + intval(substr($num2, $i, 1)) + ($t ? 1 : 0);
            $t = $s > 9;
            $result .= strval($s % 10);
        }

        $result .= $t ? '1' : '';

        return strrev($result);
    }

    private static function subInt(string $num1, string $num2, int $elderSign = 0): string
    {
        $ar1 = str_split(strrev($num1));
        $ar2 = str_split(strrev($num2));

        $diff = [];

        $l = strlen($num1);

        for ($i = 0; $i < $l; $i++) {
            $diff[$i] = $ar1[$i] - $ar2[$i];
        }

        $diff = array_reverse($diff);

        $sign = $diff[0] <=> 0;

        for ($i = 0; $i < $l; $i++) {
            if ($i > 0) {
                if ($diff[$i] < 0 && $elderSign > 0) {
                    for ($j = $i; $j > $elderIndex; $j--) {
                        $diff[$j] += 10;
                        $diff[$j - 1] -= 1;
                    }
                } elseif ($diff[$i] > 0 && $elderSign < 0) {
                    for ($j = $i; $j > $elderIndex; $j--) {
                        $diff[$j - 1] += 1;
                        $diff[$j] -= 10;
                    }
                }
            }
            if ($sign === 0) {
                $sign = $diff[$i] <=> 0;
            }
            if ($diff[$i] !== 0) {
                $elderSign = $diff[$i] <=> 0;
                $elderIndex = $i;
            }
        }

        $result = array_map('abs', $diff);

        return ($sign < 0 ? '-' : '') .join('', $result);
    }

    private static function ztrim($string)
    {
        $negative = substr($string, 0, 1) === '-';
        $fraction = strpos($string, '.') !== false;

        if ($negative) {
            $string = substr($string, 1);
        }

        $string = ltrim($string, '0');

        if ($fraction) {
            $string = rtrim($string, '0');
            $string = rtrim($string, '.');
        }

        if ($string === '') {
            return '0';
        }

        return ($negative ? '-' : '') . $string;
    }

    private static function subPositiveFromPositive($int1, $fract1, $int2, $fract2): string
    {
        $fullSumm = self::subInt($int1.$fract1, $int2.$fract2);
        if (substr($fullSumm, 0, 1) === '-') {
            $intSum = substr($fullSumm, 0, strlen($int1) + 1);
            $fractSum = substr($fullSumm, strlen($int1) + 1);
        } else {
            $intSum = substr($fullSumm, 0, strlen($int1));
            $fractSum = substr($fullSumm, strlen($int1));
        }
        return self::ztrim(sprintf('%s.%s', $intSum, $fractSum));
    }

    public static function add($num1, $num2): string
    {
        $num1 = strval($num1);
        $num2 = strval($num2);

        if (!preg_match('#^(\-?)(\d+)(\.\d+)?$#', $num1, $matches1)) {
            throw new \InvalidArgumentException('Argument 1 should be number string, '.$num1.' given');
        }

        if (!preg_match('#^(\-?)(\d+)(\.\d+)?$#', $num2, $matches2)) {
            throw new \InvalidArgumentException('Argument 2 should be number string, '.$num2.' given');
        }

        $isNegative1 = $matches1[1] === '-';
        $int1 = $matches1[2];
        $fract1 = !empty($matches1[3]) ? substr($matches1[3], 1) : '';

        $isNegative2 = $matches2[1] === '-';
        $int2 = $matches2[2];
        $fract2 = !empty($matches2[3]) ? substr($matches2[3], 1) : '';

        $intLength = max(strlen($int1), strlen($int2));
        $int1 = str_pad($int1, $intLength, '0', STR_PAD_LEFT);
        $int2 = str_pad($int2, $intLength, '0', STR_PAD_LEFT);

        $fractLength = max(strlen($fract1), strlen($fract2));
        $fract1 = str_pad($fract1, $fractLength, '0', STR_PAD_RIGHT);
        $fract2 = str_pad($fract2, $fractLength, '0', STR_PAD_RIGHT);

        if ($isNegative1 && $isNegative2) {
            $fractSum = self::addInt($fract1, $fract2);
            if (strlen($fractSum) > $fractLength) {
                $intSum = self::addInt($int1, $int2, true);
                $fractSum = substr($fractSum, 1);
            } else {
                $intSum = self::addInt($int1, $int2, false);
            }
            $intSum = ltrim($intSum, '0') ?: '0';
            $fractSum = rtrim($fractSum, '0');
            return self::ztrim('-' . $intSum . ($fractSum ? '.'.$fractSum : ''));

        } elseif (!$isNegative1 && !$isNegative2) {
            $fractSum = self::addInt($fract1, $fract2);
            if (strlen($fractSum) > $fractLength) {
                $intSum = self::addInt($int1, $int2, true);
                $fractSum = substr($fractSum, 1);
            } else {
                $intSum = self::addInt($int1, $int2, false);
            }
            $fractSum = rtrim($fractSum, '0');
            $intSum = ltrim($intSum, '0') ?: '0';
            return $intSum . ($fractSum ? '.'.$fractSum : '');

        } elseif ($isNegative1) {
            return self::subPositiveFromPositive($int2, $fract2, $int1, $fract1);
        } else {
            return self::subPositiveFromPositive($int1, $fract1, $int2, $fract2);
        }
    }
}
