<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Image\Tests\Generator;

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
        $this->templateEngine  = $this->getMock('\PUGX\Badge\Image\Template\TemplateEngineInterface');
        $this->shieldGenerator = new SvgShieldGenerator($this->templateEngine, 'test');
    }

    /**
     * @dataProvider getShieldData
     */
    public function testItGenerateAShieldFromVendorValueAndColor($vendor, $value, $color, $expectedParameters)
    {
        if ($this->hasGDTheVersion2()) {
            $this->markTestSkipped('Need version 2 of GD image functions');
        }

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

    /**
     * @return bool
     */
    private function hasGDTheVersion2()
    {
        $gdInfo = gd_info(); // array of GD version
        $gdInfo = $gdInfo['GD Version'][0]; // first char

        return $gdInfo != '2';
    }
}
