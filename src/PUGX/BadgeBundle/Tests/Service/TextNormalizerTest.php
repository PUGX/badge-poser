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
use PUGX\BadgeBundle\Service\TextNormalizer;

class TextNormalizerTest extends WebTestCase
{
    private $normalizer;

    public function setUp()
    {
        $this->normalizer = new TextNormalizer();
    }

    /**
     * @dataProvider getBadNumberToConvert
     * @expectedException InvalidArgumentException
     */
    public function testNumberToTextConversion($input, $output)
    {
        $res = $this->normalizer->normalize($input);
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
        $res = $this->normalizer->normalize($input);
        $this->assertEquals($output, $res);
    }

    public static function getGoodNumberToConvert()
    {
        return array(
            array(0,               '1'),
            array(1,               '1'),
            array('16',            '16'),
            array(199,             '199'),
            array('1012',          '1.01 k'),
            array('1212',          '1.21 k'),
            array('1999',          '2 k'),
            array('1003000',       '1 M'),
            array(9001003000,      '9 G'),
            array('9001003000000', '9 T'),
        );
    }
}
