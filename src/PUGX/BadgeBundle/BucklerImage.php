<?php

namespace PUGX\BadgeBundle;

use Imagine\Draw\DrawerInterface;
use Imagine\Effects\EffectsInterface;
use Imagine\Exception\OutOfBoundsException;
use Imagine\Exception\RuntimeException;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\Fill\FillInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\InvalidArgumentException;
use Imagine\Image\LayersInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\PointInterface;

class BucklerImage implements ImageInterface
{
    private $options;

    public function __construct($vendor, $status, $color)
    {
        $this->options = array('vendor' => $vendor, 'status' => $status, 'color' => $color);
    }

    /**
     * Returns the image content as a binary string
     *
     * @param string $format
     * @param array  $options
     *
     * @throws RuntimeException
     *
     * @return string binary
     */
    public function get($format, array $options = array())
    {
        return $this->options;
    }

    /**
     * Returns the image content as a PNG binary string
     *
     * @throws RuntimeException
     *
     * @return string binary
     */
    public function __toString()
    {
        throw new \Exception('Not implementedImplement __toString');
    }

    /**
     * Instantiates and returns a DrawerInterface instance for image drawing
     *
     * @return DrawerInterface
     */
    public function draw()
    {
        throw new \Exception('Not implementedImplement draw');
    }

    /**
     * @return EffectsInterface
     */
    public function effects()
    {
        throw new \Exception('Not implementedImplement effects');
    }

    /**
     * Returns current image size
     *
     * @return BoxInterface
     */
    public function getSize()
    {
        throw new \Exception('Not implementedImplement getSize');
    }

    /**
     * Transforms creates a grayscale mask from current image, returns a new
     * image, while keeping the existing image unmodified
     *
     * @return ImageInterface
     */
    public function mask()
    {
        throw new \Exception('Not implementedImplement mask');
    }

    /**
     * Returns array of image colors as Imagine\Image\Color instances
     *
     * @return array
     */
    public function histogram()
    {
        throw new \Exception('Not implementedImplement histogram');
    }

    /**
     * Returns color at specified positions of current image
     *
     * @param PointInterface $point
     *
     * @throws RuntimeException
     *
     * @return Color
     */
    public function getColorAt(PointInterface $point)
    {
        throw new \Exception('Not implementedImplement getColorAt');
    }

    /**
     * Returns the image layers when applicable.
     *
     * @throws RuntimeException     In case the layer can not be returned
     * @throws OutOfBoundsException In case the index is not a valid value
     *
     * @return LayersInterface
     */
    public function layers()
    {
        throw new \Exception('Not implementedImplement layers');
    }

    /**
     * Enables or disables interlacing
     *
     * @param string $scheme
     *
     * @throws InvalidArgumentException When an unsupported Interface type is supplied
     *
     * @return ImageInterface
     */
    public function interlace($scheme)
    {
        throw new \Exception('Not implementedImplement interlace');
    }

    /**
     * Copies current source image into a new ImageInterface instance
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function copy()
    {
        throw new \Exception('Not implementedImplement copy');
    }

    /**
     * Crops a specified box out of the source image (modifies the source image)
     * Returns cropped self
     *
     * @param PointInterface $start
     * @param BoxInterface   $size
     *
     * @throws OutOfBoundsException
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function crop(PointInterface $start, BoxInterface $size)
    {
        throw new \Exception('Not implementedImplement crop');
    }

    /**
     * Resizes current image and returns self
     *
     * @param BoxInterface $size
     * @param string       $filter
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function resize(BoxInterface $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        throw new \Exception('Not implementedImplement resize');
    }

    /**
     * Rotates an image at the given angle.
     * Optional $background can be used to specify the fill color of the empty
     * area of rotated image.
     *
     * @param integer $angle
     * @param Color   $background
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function rotate($angle, Color $background = null)
    {
        throw new \Exception('Not implementedImplement rotate');
    }

    /**
     * Pastes an image into a parent image
     * Throws exceptions if image exceeds parent image borders or if paste
     * operation fails
     *
     * Returns source image
     *
     * @param ImageInterface $image
     * @param PointInterface $start
     *
     * @throws InvalidArgumentException
     * @throws OutOfBoundsException
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function paste(ImageInterface $image, PointInterface $start)
    {
        throw new \Exception('Not implementedImplement paste');
    }

    /**
     * Saves the image at a specified path, the target file extension is used
     * to determine file format, only jpg, jpeg, gif, png, wbmp and xbm are
     * supported
     *
     * @param string $path
     * @param array  $options
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function save($path, array $options = array())
    {
        throw new \Exception('Not implementedImplement save');
    }

    /**
     * Outputs the image content
     *
     * @param string $format
     * @param array  $options
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function show($format, array $options = array())
    {
        throw new \Exception('Not implementedImplement show');
    }

    /**
     * Flips current image using horizontal axis
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function flipHorizontally()
    {
        throw new \Exception('Not implementedImplement flipHorizontally');
    }

    /**
     * Flips current image using vertical axis
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function flipVertically()
    {
        throw new \Exception('Not implementedImplement flipVertically');
    }

    /**
     * Remove all profiles and comments
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function strip()
    {
        throw new \Exception('Not implementedImplement strip');
    }

    /**
     * Generates a thumbnail from a current image
     * Returns it as a new image, doesn't modify the current image
     *
     * @param BoxInterface $size
     * @param string       $mode
     *
     * @throws RuntimeException
     *
     * @return ManipulatorInterface
     */
    public function thumbnail(BoxInterface $size, $mode = self::THUMBNAIL_INSET)
    {
        throw new \Exception('Not implementedImplement thumbnail');
    }

    /**
     * Applies a given mask to current image's alpha channel
     *
     * @param ImageInterface $mask
     *
     * @return ManipulatorInterface
     */
    public function applyMask(ImageInterface $mask)
    {
        throw new \Exception('Not implementedImplement applyMask');
    }

    /**
     * Fills image with provided filling, by replacing each pixel's color in
     * the current image with corresponding color from FillInterface, and
     * returns modified image
     *
     * @param FillInterface $fill
     *
     * @return ManipulatorInterface
     */
    public function fill(FillInterface $fill)
    {
        throw new \Exception('Not implementedImplement fill');
    }

}
