<?php

namespace App\Badge\Service;

/**
 * Transform numbers to readable format.
 */
interface NormalizerInterface
{
    public function normalize(float | int | string | null $number, int $precision = 2): string;
}
