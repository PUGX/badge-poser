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
            //bad number return Exception
            array('A',             'ERR 2 ', 'PUGX\BadgeBundle\Exception\InvalidArgumentException'),
            array(-1,              'ERR 2 ', 'PUGX\BadgeBundle\Exception\InvalidArgumentException'),

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
        $imageCreator = new ImageCreator($this->logger, 'font', 'image');
        $res = $imageCreator->transformNumberToReadableFormat($input);
        if (null === $withException) {
            $this->assertEquals($imageCreator->transformNumberToReadableFormat($input), $res);
        }
    }
}
