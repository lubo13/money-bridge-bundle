<?php

/**
 * @package Money\Bridge\MoneyBridgeBundle
 * @author Lubo Grozdanov <grozdanov.lubo@gmail.com>
 */

declare(strict_types=1);

namespace Money\Bridge\Util;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;

class AmountFormatter
{
    public const MONEY_FRACTIONAL_PART = 2;

    /**
     * Format amount in the following ways:
     *     12345678901.01 -> 1234567890101
     *     999111111119.99 -> 99911111111999
     *     00001.01 -> 101
     *     0000.01 -> 1
     *     0000.10 -> 10
     *     00100.10 -> 10001
     *     0 -> 0
     *     00000 -> 0
     *     00000.1 -> 10
     *     .1 -> 10
     *     .01 -> 1
     *     00100.00 -> 10000
     *     300100 -> 30010000
     *     00304 -> 30400
     *
     * @param string $amount
     * @param int|null $fractionalPart
     *
     * @return string
     */
    public static function getAmountWithoutFractional(string $amount, int $fractionalPart = null): string
    {
        if (null === $fractionalPart) {
            $fractionalPart = self::MONEY_FRACTIONAL_PART;
        }

        $explode = explode('.', $amount);
        $explode[0] = $explode[0] !== '' ? $explode[0] : '0';

        if (! is_numeric($explode[0]) || (isset($explode[1]) && $explode[1] !== '' && ! is_numeric($explode[1]))) {
            throw new InvalidArgumentException('The amount should be numeric.');
        }

        $amount = ltrim($explode[0] . str_pad($explode[1] ?? '', $fractionalPart, '0'), '0');

        return $amount !== '' ? $amount : '0';
    }
}
