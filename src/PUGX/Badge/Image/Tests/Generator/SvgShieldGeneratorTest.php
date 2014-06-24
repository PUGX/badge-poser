<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Image\Generator {
    // travis fails the calculation of the box size... faked!
    function imagettfbbox($size, $angle, $fontfile, $text) {
        global $mockImagettfbbox;

        if (isset($mockImagettfbbox) && $mockImagettfbbox != false) {
            return $mockImagettfbbox[$text];
        } else {
            return call_user_func_array('\imagettfbbox', func_get_args());
        }
    }

}


namespace PUGX\Badge\Image\Tests\Generator {

use PUGX\Badge\Image\Generator\SvgShieldGenerator;
use PUGX\Badge\Image\Generator\SvgShieldGeneratorInterface;


/**
 * Class SvgShieldGeneratorTest
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 */
class SvgShieldGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var SvgShieldGeneratorInterface $shieldGenerator */
    public $shieldGenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject $templateEngine */
    public $templateEngine;

    public function setUp()
    {
        global $mockImagettfbbox;
        $mockImagettfbbox = array(
            'testVendor' => array(-2, 0, 61, 0, 61, -11, -2, -11),
            'testValue'  => array(-2, 0, 52, 0, 52, -11, -2, -11)
        );
        $this->templateEngine  = $this->getMock('\PUGX\Badge\Image\Template\TemplateEngineInterface');
        $this->shieldGenerator = new SvgShieldGenerator($this->templateEngine, 'test');
    }

    /**
     * @dataProvider getShieldData
     */
    public function testItGenerateAShieldFromVendorValueAndColor($vendor, $value, $color, $expectedParameters)
    {
        $this->templateEngine->expects($this->once())
                             ->method('render')
                             ->with($this->identicalTo('test'), $this->equalTo($expectedParameters));

        $this->shieldGenerator->generateShield($vendor, $value, $color);
    }

    public function getShieldData()
    {
        return array(
            array(
                'testVendor', 'testValue', 'red', array(
                'vendorWidth'         => 73.0,
                'valueWidth'          => 64.0,
                'totalWidth'          => 137.0,
                'vendorColor'         => '#555',
                'valueColor'          => '#e05d44',
                'vendor'              => 'testVendor',
                'value'               => 'testValue',
                'vendorStartPosition' => 37.5,
                'valueStartPosition'  => 104.0
            )
        )
        );
    }
}
}