<?php

namespace App\Badge\Service;

/**
 * Interface NormalizerInterface
 * Transform numbers to readable format.
 */
interface NormalizerInterface
{
    public function normalize($number, $precision = 2);
}
