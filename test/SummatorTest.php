<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class SummatorTest extends TestCase
{
    /**
     * Remove float trail
     */
    public static function rft($string)
    {
        if (strpos($string, '.') !== false) {
            return rtrim(rtrim($string, '0'), '.');
        }
        return $string;
    }

    /**
     * Negativize number
     */
    public static function n($string)
    {
        if (substr($string, 0, 1) === '-') {
            return substr($string, 1);
        }
        return '-'.$string;
    }

    /**
     * @dataProvider dataProviderForTest
     */
    public function test($num1, $num2)
    {
        $num1 = strval($num1);
        $num2 = strval($num2);

        /* Both Positive */
        self::assertSame(self::rft(bcadd($num1, $num2, 100)),                   Summator::add($num1, $num2));
        self::assertSame(self::rft(bcadd($num1, $num2, 100)),                   Summator::add($num2, $num1));

        /* Positive & Negative */
        self::assertSame(self::rft(bcsub($num1, $num2, 100)),                   Summator::add($num1, self::n($num2)));
        self::assertSame(self::rft(bcsub($num1, $num2, 100)),                   Summator::add(self::n($num2), $num1));

        /* Negative & Positive */
        self::assertSame(self::rft(bcsub($num2, $num1, 100)),                   Summator::add(self::n($num1), $num2));
        self::assertSame(self::rft(bcsub($num2, $num1, 100)),                   Summator::add($num2, self::n($num1)));

        /* Both Negative */
        self::assertSame(self::rft(bcadd(self::n($num1), self::n($num2), 100)), Summator::add(self::n($num2), self::n($num1)));
        self::assertSame(self::rft(bcadd(self::n($num1), self::n($num2), 100)), Summator::add(self::n($num1), self::n($num2)));
    }

    public function dataProviderForTest()
    {
        return [
            [0, 0],
            ['0.0', '0.0'],
            ['1.05', '10.55555'],
            ['1.9', '1.9'],
            ['001.9', '1.9'],
            ['1.9', '1.900'],
            [PHP_INT_MAX, PHP_INT_MAX],
            ['18446744073709551614', '18446744073709551614'],
            ['9223372036854775807', '1'],
            [10000, 1111],
            [123456, 12345],
            [2.2, 1.1],
            [123456.123456, 12345.12345],
            [10000000000001, 999],
            [99999999999, 0],
            [1, 1000],
            ['1213131555087879800000007789856546548878987.1210000000087879845454698877898565465488780',
                '42100000058878745454698877898565465488789.121313155588787000000000000000000046548878987']
        ];
    }
}
