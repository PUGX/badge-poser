<?php

namespace App\Badge\Service;

use InvalidArgumentException;

/**
 * Transform numbers to readable format.
 */
class TextNormalizer
{
    /**
     * @param $number
     * @param  int    $precision
     * @return string
     */
    public function normalize($number, $precision = 2)
    {
            $number = $this->normalizeNumber($number);
            $units = ['', ' k', ' M', ' G', ' T'];

            $pow = floor(($number ? log($number) : 0) / log(1000));
            $pow = min($pow, count($units) - 1);

            $number /= pow(1000, $pow);

             return round($number, $precision) . $units[$pow];
    }

    /**
     * This function transform a number to a float value or raise an Exception.
     *
     * @param mixed $number number to be normalized
     *
     * @return int
     * @throws InvalidArgumentException
     */
    private function normalizeNumber($number)
    {
        if (!is_numeric($number)) {
            throw new InvalidArgumentException('Number expected');
        }

        $number = floatval($number);

        if ($number < 0) {
            throw new InvalidArgumentException('The number expected was >= 0');
        }

        return max($number, 1);
    }
}
