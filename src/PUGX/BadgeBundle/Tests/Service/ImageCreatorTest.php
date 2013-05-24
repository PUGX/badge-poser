<?php

namespace PUGX\BadgeBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\BadgeBundle\Service\ImageCreator;


class ImageCreatorTest extends WebTestCase
{

    private $logger;
    private $packagistClient;

    public function setUp() {
        $this->logger = $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->packagistClient = $this->getMock('Packagist\Api\Client');
    }

    public static function provider()
    {
        return array(
            //bad number return 1
            array('A',             'ERR 2 '),
            array(-1,              'ERR 2 '),
            //
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
     * @dataProvider provider
     */
    public function testNumberToTextConversion($input, $output)
    {
        $imageCreator = new ImageCreator($this->logger, 'font', 'image');
        $this->assertEquals($imageCreator->transformNumberToReadableFormat($input), $output);
    }
}
