<?php

namespace App\Badge\Service;

use InvalidArgumentException;

/**
 * Class TextNormalizer
 * Transform numbers to readable format.
 */
final class TextNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $number
     * @param int   $precision
     *
     * @throws InvalidArgumentException
     */
    public function normalize($number, $precision = 2): string
    {
        $number = $this->normalizeNumber($number);
        $units = ['', ' k', ' M', ' G', ' T'];

        $pow = floor(($number ? log($number) : 0) / log(1000));
        $pow = min($pow, \count($units) - 1);

        $number /= 1000 ** $pow;

        return round($number, $precision).$units[$pow];
    }

    /**
     * This function transform a number to a float value or raise an Exception.
     *
     * @param mixed $number number to be normalized
     *
     * @throws InvalidArgumentException
     */
    private function normalizeNumber($number): int
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException('Number expected');
        }

        $number = (float) $number;

        if ($number < 0) {
            throw new InvalidArgumentException('The number expected was >= 0');
        }

        return max($number, 1);
    }
}
