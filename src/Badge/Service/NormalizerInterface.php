<?php

namespace App\Badge\Service;

/**
 * Interface NormalizerInterface
 * Transform numbers to readable format.
 * @package App\Badge\Service
 */
interface NormalizerInterface
{
    public function normalize($number, $precision = 2);
}
