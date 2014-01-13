<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\BadgeBundle\Service\ImageCreator;
use Imagine\Gd\Imagine;

class ImageCreatorTest extends WebTestCase
{
    private $logger;
    private $packagistClient;
    private $fontPath;
    private $imagesPath;
    private $imageCreator;

    public function setUp()
    {
        $this->logger = \Phake::mock('Symfony\Bridge\Monolog\Logger');

        $this->packagistClient = \Phake::mock('Packagist\Api\Client');
        $this->imagine = new Imagine();

        $kernelDir = $_SERVER['KERNEL_DIR'];

        $this->fontPath = $kernelDir . '/Resources/badge-assets/fonts';
        $this->imagesPath = $kernelDir . '/Resources/badge-assets/images';

        $this->imageCreator = new ImageCreator($this->logger, $this->imagine, $this->fontPath, $this->imagesPath);
    }

    /**
     * @dataProvider getBadNumberToConvert
     * @expectedException InvalidArgumentException
     */
    public function testNumberToTextConversion($input, $output)
    {
        $res = $this->imageCreator->transformNumberToReadableFormat($input);
        $this->assertEquals($output, $res);
    }

    public static function getBadNumberToConvert()
    {
        return array(
            array('A', 'ERR 2 '),
            array(-1, 'ERR 2 '),
        );
    }

    /**
     * @dataProvider getGoodNumberToConvert
     */
    public function testGoodNumberToTextConversion($input, $output)
    {
        $res = $this->imageCreator->transformNumberToReadableFormat($input);
        $this->assertEquals($output, $res);
    }

    public static function getGoodNumberToConvert()
    {
        return array(
            array(0,               '   1  '),
            array(1,               '   1  '),
            array('16',            '  16  '),
            array(199,             ' 199  '),
            array('1012',          '1.01 k'),
            array('1999',          '2.00 k'),
            array('1003000',       '1.00 m'),
            array(9001003000,      '9.0 mm'),
            array('9001003000000', '9.0 bb'),
        );
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testAddShadowedText_withBadImage()
    {

        $reflectionMethod = new \ReflectionMethod($this->imageCreator, 'addShadowedText');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invokeArgs($this->imageCreator, array(false, 'test text'));
    }

    public function provideShadow()
    {
        return array(
            array(false, 'downloads.png'),
            array(true, 'downloads.png')
        );
    }

    /**
     *
     * @dataProvider provideShadow
     */
    public function testAddShadowedText_maintainsOriginalDimension($withShadow, $imageFile)
    {
        $reflectionMethod = new \ReflectionMethod($this->imageCreator, 'addShadowedText');
        $reflectionMethod->setAccessible(true);

        $image = $this->imagine->open($this->imagesPath . DIRECTORY_SEPARATOR . $imageFile);

        $expectedWidth = $image->getSize()->getWidth();
        $expectedHeight = $image->getSize()->getHeight();

        $reflectionMethod->invokeArgs($this->imageCreator, array($image, 'TEST_TEXT', 3, 13, 8.5, null, $withShadow));

        $this->assertEquals($expectedWidth, $image->getSize()->getWidth(), 'The method should not modify the image width');
        $this->assertEquals($expectedHeight, $image->getSize()->getHeight(), 'The method should not modify the image height');
    }

    public function testCreateImage()
    {
        $reflectionMethod = new \ReflectionMethod($this->imageCreator, 'createImage');
        $reflectionMethod->setAccessible(true);
        $image = $reflectionMethod->invokeArgs(
            $this->imageCreator,
            array($this->imagesPath . DIRECTORY_SEPARATOR . 'empty.png')
        );

        $this->assertInstanceOf('Imagine\Image\ImageInterface', $image);
    }

    /**
     * @expectedException \Imagine\Exception\InvalidArgumentException
     */
    public function testCreateImage_throwException()
    {
        $reflectionMethod = new \ReflectionMethod($this->imageCreator, 'createImage');
        $reflectionMethod->setAccessible(true);
        $image = $reflectionMethod->invokeArgs(
            $this->imageCreator,
            array($this->imagesPath . DIRECTORY_SEPARATOR . 'invalid_file.png')
        );
    }

    public function tearDown()
    {
        $this->logger = null;
        $this->packagistClient = null;
        $this->imageCreator = null;
    }
}
