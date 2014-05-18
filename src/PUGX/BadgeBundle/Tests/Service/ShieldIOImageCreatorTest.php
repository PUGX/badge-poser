<?php

namespace PUGX\BadgeBundle\Tests\Service;

use PUGX\BadgeBundle\Service\ShieldIOImageCreator;

class ShieldIOImageCreatorTest extends \PHPUnit_Framework_TestCase
{
    private $httpClient;
    private $imageCreator;

    public function setUp()
    {
        $this->httpClient = $this->getMock('\Guzzle\Http\ClientInterface');
        $this->imageCreator = new ShieldIOImageCreator($this->httpClient);
    }

    public function testShouldCreateDownloadsImage()
    {
        $response = $this->getMockBuilder('\Guzzle\Http\Message\Response')
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->httpClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo('http://img.shields.io/badge/downloads-test-blue.svg'));

        $this->httpClient->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $image = $this->imageCreator->createDownloadsImage('test');
        $this->assertInstanceOf('\PUGX\BadgeBundle\ImageInterface', $image);
    }

}
