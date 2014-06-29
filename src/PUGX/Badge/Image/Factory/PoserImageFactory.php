<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Image\Factory;


use PUGX\Badge\Image\ImageFactoryInterface;
use PUGX\Badge\Image\Image;
use PUGX\Poser\Poser;

/**
 * Class PoserImageFactory
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
class PoserImageFactory implements ImageFactoryInterface
{
    /**
     * @var array $definedColors
     */
    private static $definedColors = array(
        self::DOWNLOADS => 'blue',
        self::STABLE    => '28a3df',
        self::UNSTABLE  => 'e68718',
        self::ERROR     => 'red',
        self::LICENSE   => '428F7E'
    );

    /**
     * @var Poser $shieldGenerator
     */
    private $shieldGenerator;

    public function __construct(Poser $poser)
    {
        $this->shieldGenerator = $poser;
    }

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createDownloadsImage($value)
    {
        $shield = $this->generate(self::DOWNLOADS, $value, self::$definedColors[self::DOWNLOADS]);

        return Image::createFromString($shield);
    }

    /**
     * Create the 'stable:no release' image with the standard Font and stable image template.
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createStableNoImage($value)
    {
        $shield = $this->generate(self::STABLE, $value, self::$definedColors[self::STABLE]);

        return Image::createFromString($shield);
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
        $shield = $this->generate(self::STABLE, $value, self::$definedColors[self::STABLE]);

        return Image::createFromString($shield);
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
        $shield = $this->generate(self::UNSTABLE, $value, self::$definedColors[self::UNSTABLE]);

        return Image::createFromString($shield);
    }

    /**
     * Create the 'error' image
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createErrorImage($value)
    {
        $shield = $this->generate(self::ERROR, $value, self::$definedColors[self::ERROR]);

        return Image::createFromString($shield);
    }

    /**
     * Create a 'license' Image
     *
     * @param string $value
     *
     * @return \PUGX\Badge\Image\Image
     */
    public function createLicenseImage($value)
    {
        $shield = $this->generate(self::LICENSE, $value, self::$definedColors[self::LICENSE]);

        return Image::createFromString($shield);
    }

    /**
     * @param string $vendor
     * @param string $value
     * @param string $color
     *
     * @return string
     */
    private function generate($vendor, $value, $color)
    {
        return $this->shieldGenerator->generate($vendor, $value, $color, 'svg');
    }
}
