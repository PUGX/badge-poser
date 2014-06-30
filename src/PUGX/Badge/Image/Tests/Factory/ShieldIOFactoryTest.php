<?php

namespace PUGX\Badge\Image\Tests\Factory;

use Guzzle\Http\Message\Response;
use PUGX\Badge\Image\Factory\ShieldIOFactory;

class ShieldIOFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $httpClient;
    private $imageCreator;
    private $urlGenerator;

    public function setUp()
    {
        $this->httpClient = $this->getMock('\Guzzle\Http\ClientInterface');
        $this->urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $this->imageCreator = new ShieldIOFactory($this->httpClient, $this->urlGenerator);
    }

    public function testShouldCreateDownloadsImage()
    {
        $response = Response::fromMessage("HTTP/1.1 Hello.\r\n\r\n");

        $this->httpClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo('http://img.shields.io/badge/downloads-test-blue.svg'));

        $this->httpClient->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('pugx_badge_shieldio', $this->equalTo(array('vendor'=>'downloads', 'value'=>'test', 'color'=>'blue', 'extension'=>'svg')), true)
            ->will($this->returnValue('http://img.shields.io/badge/downloads-test-blue.svg'));

        $image = $this->imageCreator->createDownloadsImage('test');
        $this->assertInstanceOf('PUGX\Badge\Image\Image', $image);
    }
}
