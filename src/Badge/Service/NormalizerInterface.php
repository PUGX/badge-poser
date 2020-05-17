<?php

namespace App\Badge\Service;

/**
 * Interface NormalizerInterface
 * Transform numbers to readable format.
 */
interface NormalizerInterface
{
    /**
     * @param string|int|float $number
     */
    public function normalize($number, int $precision = 2): string;
}
