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

use Symfony\Bridge\Monolog\Logger;
use PUGX\BadgeBundle\Exception\InvalidArgumentException;
use PUGX\BadgeBundle\Event\PackageEvent;


class ImageCreator implements ImageCreatorInterface
{
    CONST ERROR_TEXT_GENERIC = 'ERR 1 ';
    CONST ERROR_TEXT_NOT_A_NUMBER = 'ERR 2 ';
    CONST ERROR_TEXT_CLIENT_EXCEPTION = 'ERR 3 ';

    private $logger;
    protected $dispatcher;
    protected $imageNames = array('empty' => 'empty.png', 'downloads' => 'downloads.png', 'stable' => 'stable.png', 'unstable' => 'unstable.png');
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
            'bb' => 1000000000000,
            'mm' => 1000000000,
            'm'  => 1000000,
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
     * Add a shadowed text to an Image.
     *
     * @param resource $image
     * @param string   $text
     * @param int      $x
     * @param int      $y
     * @param float    $size
     * @param string   $font
     * @param bool     $withShadow
     * @param int      $angle
     */
    private function addShadowedText($image, $text, $x = 3, $y = 13, $size = 8.5, $font = null, $withShadow = true, $angle = 0)
    {
        if (null ===  $font) {
            $font = $this->fontPath . DIRECTORY_SEPARATOR . $this->defaultFont;
        }

        $white = imagecolorallocate($image, 255, 255, 250);
        $black = imagecolorallocate($image, 0, 0, 0);
        if ($withShadow) {
            $imageArray = imagettftext($image, $size, $angle, $x+1, $y+1, $black, $font, $text);
        }
        $imageArray = imagettftext($image, $size, $angle, $x, $y, $white, $font, $text);
    }

    /**
     * Create the image resource, with Blending and Alpha.
     *
     * @param string $imagePath
     *
     * @return resource
     */
    private function createImage($imagePath)
    {
        $image = imagecreatefrompng($imagePath);
        imageAlphaBlending($image, true);
        imageSaveAlpha($image, true);

        return $image;
    }

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createDownloadsImage($value)
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['downloads'];
        $image =  $this->createImage($imagePath);

        $this->addShadowedText($image, $value, 64, 13.5);

        return $image;
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createStableImage($value)
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['stable'];
        $image =  $this->createImage($imagePath);

        $this->addShadowedText($image, $value, 52, 13.5);

        return $image;
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createUnstableImage($value = '@dev')
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['unstable'];
        $image =  $this->createImage($imagePath);
;
        $this->addShadowedText($image, $value, 52, 13.5, 7);

        return $image;
    }
}