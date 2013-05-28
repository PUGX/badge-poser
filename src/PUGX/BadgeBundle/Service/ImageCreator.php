<?php

/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use PUGX\BadgeBundle\Exception\InvalidArgumentException;
use Symfony\Bridge\Monolog\Logger;
use PUGX\BadgeBundle\Event\PackageEvent;


class ImageCreator
{
    CONST ERROR_TEXT_GENERIC = 'ERR 1 ';
    CONST ERROR_TEXT_NOT_A_NUMBER = 'ERR 2 ';
    CONST ERROR_TEXT_CLIENT_EXCEPTION = 'ERR 3 ';

    private $logger;
    protected $dispatcher;
    protected $imageNames = array('empty' => 'empty.png', 'downloads' => 'download.png');
    protected $imagePath;
    protected $fontPath;
    protected $defaultFont;
    protected $defaultImage;

    public function __construct(Logger $logger, $fontPath, $imagePath, $defaultFont = null, $defaultImage = null)
    {
        $this->logger = $logger;
        $this->fontPath = $fontPath;
        $this->imagePath = $imagePath;

        if (!$defaultFont) {
            $defaultFont = 'DroidSans.ttf';
        }
        if (!$defaultImage) {
            $defaultImage = $this->imageNames['empty'];
        }
        $this->defaultFont = $defaultFont;
        $this->defaultImage = $defaultImage;
    }

    /**
     * This function transform a number to a float value or raise an Exception.
     *
     * @param mixed $number
     *
     * @return int
     * @throws \PUGX\BadgeBundle\Exception\InvalidArgumentException
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


    /**
     * Function that should return a human readable number in a maximum number of chars.
     *
     * @param int $number
     * @param int $maxChar
     *
     * @return string
     * @throws \PUGX\BadgeBundle\Exception\InvalidArgumentException
     */
    public function transformNumberToReadableFormat($number, $maxChar = 6)
    {
        $defaultFormatter = '%.0f %s';
        $dimensions = array(
            1000000000000 => 'bb',
            1000000000 => 'mm',
            1000000 => 'm',
            1000 => 'k',
            1 => ' ',
        );

        $number = $this->normalizeNumber($number);

        foreach ($dimensions as $key => $suffix) {
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
     * Stream the output.
     *
     * @param resource $image
     *
     * @return Boolean
     */
    public function streamRawImageData($image)
    {
        return imagepng($image);
    }

    /**
     * Destroy the resource.
     *
     * @param $image
     *
     * @return Boolean
     */
    public function destroyImage($image)
    {
        return imagedestroy($image);
    }

    /**
     * Create the image resource.
     *
     * @param $text
     * @param $value
     * @param $imagePath
     * @param $fontPath
     * @param $withShadow
     *
     * @return resource
     */
    public function createImage($text, $value, $imagePath, $fontPath, $withShadow = true)
    {
        $image = imagecreatefrompng($imagePath);
        $white = imagecolorallocate($image, 255, 255, 250);
        $black = imagecolorallocate($image, 0, 0, 0);
        $font_path = $fontPath;

        //text
        if ($withShadow) {
            $imageArray = imagettftext($image, 8.5, 0, 4, 14, $black, $font_path, $text);
        }
        $imageArray = imagettftext($image, 8.5, 0, 3, 13, $white, $font_path, $text);

        // value
        if ($withShadow) {
            $imageArray = imagettftext($image, 8, 0, 67, 14.5, $black, $font_path, $value);
        }
        $imageArray = imagettftext($image, 8, 0, 66, 13.5, $white, $font_path, $value);

        return $image;
    }

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     * @param string $text
     *
     * @return resource
     */
    public function createDownloadsImage($value, $text = 'downloads')
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['empty'];
        $fontPath = $this->fontPath . DIRECTORY_SEPARATOR . $this->defaultFont;

        return $this->createImage($text, $value, $imagePath, $fontPath, true);
    }
}