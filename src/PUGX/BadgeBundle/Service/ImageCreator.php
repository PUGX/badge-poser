<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use Symfony\Bridge\Monolog\Logger;
use \InvalidArgumentException;

/**
 * Class ImageCreator
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 */
class ImageCreator implements ImageCreatorInterface
{
    private $logger;
    protected $dispatcher;
    protected $imageNames = array('empty' => 'empty.png', 'downloads' => 'downloads.png', 'stable' => 'stable.png', 'unstable' => 'unstable.png', 'error' => 'error.png');
    protected $imagePath;
    protected $fontPath;
    protected $defaultFont;
    protected $defaultImage;

    /**
     * class constructor
     *
     * @param Logger $logger       logger
     * @param string $fontPath     font path
     * @param string $imagePath    image path
     * @param string   $defaultFont  default font
     * @param null   $defaultImage default image
     */
    public function __construct(Logger $logger, $fontPath, $imagePath, $defaultFont = 'DroidSans.ttf', $defaultImage = null)
    {
        $this->logger = $logger;
        $this->fontPath = $fontPath;
        $this->imagePath = $imagePath;

        if (!$defaultImage) {
            $this->defaultImage = $this->imageNames['empty'];
        }
        $this->defaultFont = $defaultFont;
    }

    /**
     * This function transform a number to a float value or raise an Exception.
     *
     * @param mixed $number number to be normalized
     *
     * @return int
     * @throws \InvalidArgumentException
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
     * @param resource $image
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
     * @param int $number  number
     * @param int $maxChar max characters
     *
     * @return string
     * @throws \InvalidArgumentException
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
     * @param resource $image      image
     * @param string   $text       text
     * @param int      $x          x
     * @param int      $y          y
     * @param float    $size       size
     * @param string   $font       font
     * @param Boolean  $withShadow cast shadow
     * @param int      $angle      angle
     *
     * @return resource
     * @throws \UnexpectedValueException
     */
    private function addShadowedText($image, $text, $x = 3, $y = 13, $size = 8.5, $font = null, $withShadow = true, $angle = 0)
    {
        if (null === $font) {
            $font = $this->fontPath . DIRECTORY_SEPARATOR . $this->defaultFont;
        }

        $white = imagecolorallocate($image, 255, 255, 250);
        $black = imagecolorallocate($image, 0, 0, 0);

        if (false === $white ||  false === $black) {
            throw new \UnexpectedValueException(sprintf('Impossible to allocate a color with imagecolorallocate.'));
        }

        if ($withShadow) {
            if (!imagettftext($image, $size, $angle, $x + 1, $y + 1, $black, $font, $text)) {
                throw new \UnexpectedValueException('Impossible to add shadow text to the image with imagettftext.');
            }
        }

        if (!imagettftext($image, $size, $angle, $x, $y, $white, $font, $text)) {
            throw new \UnexpectedValueException('Impossible to add text to the image with imagettftext.');
        }

        return $image;
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
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Create the 'downloads' image with the standard Font and download image template.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createDownloadsImage($value)
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['downloads'];
        $image = $this->createImage($imagePath);
        $value = $this->transformNumberToReadableFormat($value);

        return $this->addShadowedText($image, $value, 64, 13.5);
    }

    /**
     * Create the 'stable' image with the standard Font and stable image template.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createStableImage($value)
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['stable'];
        $image = $this->createImage($imagePath);

        return $this->addShadowedText($image, $value, 59, 13.5);
    }

    /**
     * Create the 'stable' image with the standard Font and unstable image template.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createUnstableImage($value = '@dev')
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['unstable'];
        $image = $this->createImage($imagePath);

        return $this->addShadowedText($image, $value, 51, 12, 7);
    }

    /**
     * Create the 'error' image
     *
     * @param string $value
     *
     * @return resource
     */
    public function createErrorImage($value)
    {
        $imagePath = $this->imagePath . DIRECTORY_SEPARATOR . $this->imageNames['error'];
        $image = $this->createImage($imagePath);

        return $this->addShadowedText($image, $value, 50, 13.5, 7);
    }
}
