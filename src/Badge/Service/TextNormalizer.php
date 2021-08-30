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
     * @throws InvalidArgumentException
     */
    public function normalize(float|int|string|null $number, int $precision = 2): string
    {
        $number = $this->normalizeNumber($number);
        $units = ['', ' k', ' M', ' G', ' T'];

        $pow = \floor(($number ? \log($number) : 0) / \log(1000));
        $pow = \min($pow, \count($units) - 1);

        $number /= 1000 ** $pow;

        return \round($number, $precision).$units[$pow];
    }

    /**
     * This function transform a number to a float value or raise an Exception.
     *
     * @throws InvalidArgumentException
     */
    private function normalizeNumber(float|int|string|null $number): float
    {
        if (!\is_numeric($number)) {
            throw new InvalidArgumentException('Number expected');
        }

        $number = (float) $number;

        if ($number < 0) {
            throw new InvalidArgumentException('The number expected to be >= 0');
        }

        return \max($number, 1);
    }
}
