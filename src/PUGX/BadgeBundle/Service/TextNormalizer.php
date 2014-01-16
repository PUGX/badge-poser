<?php

namespace PUGX\BadgeBundle\Service;

use InvalidArgumentException;

/**
 * Transform numbers to readable format.
 */
class TextNormalizer
{
    /**
     * Function that should return a human readable number in a maximum number of chars.
     *
     * @param int $number  number
     * @param int $maxChar max characters
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function normalize($number, $maxChar = 6)
    {
        $defaultFormatter = '%.0f %s';
        $dimensions = array(
            'T' => 1000000000000,
            'G'  => 1000000000,
            'M'  => 1000000,
            'k'  => 1000,
            ' '  => 1,
        );

        $number = $this->normalizeNumber($number);

        foreach ($dimensions as $suffix => $key) {
            if ($number >= $key) {
                $number = $number / $key;
                // 2 is strlen(' ' . '.');  space and dot
                $floatPointNumber = $maxChar - strlen($suffix) - 2 - strlen(intval($number));
                $formatter = $defaultFormatter;
                $decimal_part = $number - floor($number);

                if ($decimal_part > 0 && $floatPointNumber > 0) {
                    $formatter = '%.' . $floatPointNumber . 'f %s';
                }

                $readable = sprintf($formatter, $number, $suffix);
                $readable = str_pad($readable, $maxChar, ' ', STR_PAD_LEFT);

                return $readable;
            }
        }

        throw new InvalidArgumentException(sprintf('impossible to transform to readable number[%s] with [%d] chars', $number, $maxChar));
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

        // avoid division by 0
        if ($number == 0) {
            $number = 1;
        }

        return $number;
    }
}
