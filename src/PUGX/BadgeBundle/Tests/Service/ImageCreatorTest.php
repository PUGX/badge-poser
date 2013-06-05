<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\BadgeBundle\Service\ImageCreator;

class ImageCreatorTest extends WebTestCase
{
    private $logger;
    private $packagistClient;
    private $fontPath;
    private $imagesPath;
    private $imageCreator;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->packagistClient = $this->getMock('Packagist\Api\Client');

        $kernelDir = $_SERVER['KERNEL_DIR'];

        $this->fontPath = $kernelDir . '/badge-assets/fonts';
        $this->imagesPath = $kernelDir . '/badge-assets/images';

        $this->imageCreator = new ImageCreator($this->logger, $this->fontPath, $this->imagesPath);
    }

    public static function provider()
    {
        return array(
            //bad number return Exception
            array('A',             'ERR 2 ', 'InvalidArgumentException'),
            array(-1,              'ERR 2 ', 'InvalidArgumentException'),

            array(0,               '   1  ', null),
            array(1,               '   1  ', null),
            array('16',            '  16  ', null),
            array(199,             ' 199  ', null),
            array('1012',          '1.01 k', null),
            array('1999',          '2.00 k', null),
            array('1003000',       '1.00 m', null),
            array(9001003000,      '9.0 mm', null),
            array('9001003000000', '9.0 bb', null),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testNumberToTextConversion($input, $output, $withException)
    {
        if (null !== $withException) {
            $this->setExpectedException($withException);
        }

        $res = $this->imageCreator->transformNumberToReadableFormat($input);
        if (null === $withException) {
            $this->assertEquals($output, $res);
        }
    }

    /**
     * @expectedException \ErrorException
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

        $image = imagecreatefrompng($this->imagesPath . DIRECTORY_SEPARATOR . $imageFile);
        $this->assertTrue(is_resource($image));
        $expectedWidth = imagesx($image);
        $expectedHeight = imagesy($image);

        $reflectionMethod->invokeArgs($this->imageCreator, array($image, 'TEST_TEXT', 3, 13, 8.5, null, $withShadow));

        $this->assertEquals($expectedWidth, imagesx($image), 'The method should not modify the image width');
        $this->assertEquals($expectedHeight, imagesy($image), 'The method should not modify the image height');

        imagedestroy($image);
    }

    public function testCreateImage()
    {
        $reflectionMethod = new \ReflectionMethod($this->imageCreator, 'createImage');
        $reflectionMethod->setAccessible(true);
        $image = $reflectionMethod->invokeArgs(
            $this->imageCreator,
            array($this->imagesPath . DIRECTORY_SEPARATOR . 'empty.png')
        );

        $this->assertTrue(is_resource($image));
    }

    /**
     * @expectedException \ErrorException
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
}
