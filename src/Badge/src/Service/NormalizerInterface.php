<?php

namespace PUGX\Badge\Service;

/**
 * Transform numbers to readable format.
 */
interface NormalizerInterface
{
    public function normalize($number, $precision = 2);
}
