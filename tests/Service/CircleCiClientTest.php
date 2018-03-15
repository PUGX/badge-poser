<?php

namespace App\Tests\Service;

use App\Service\CircleCiClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class CircleCiClientTest extends TestCase
{
    /** @var UrlGeneratorInterface */
    protected $router;

    /** @var ClientInterface */
    protected $client;

    /** @var CircleCiClient */
    protected $circleCiClient;

    protected function setUp()
    {
        $this->router = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->circleCiClient = new CircleCiClient($this->router, $this->client, 'fake-token');
    }

    public function testGetBuilds(): void
    {
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();
        $responseBody = $this->getMockBuilder(StreamInterface::class)
            ->getMockForAbstractClass();

        $responseBody->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue(json_encode([['status' => 'success']])));

        $response->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $response->expects($this->any())
            ->method('getBody')
            ->will($this->returnValue($responseBody));

        $this->client->expects($this->any())
            ->method('request')
            ->will($this->returnValue($response));

        $responseBuilds = $this->circleCiClient->getBuilds('pugx/badge-poser');

        $this->assertInstanceOf(ResponseInterface::class, $responseBuilds);
        $this->assertEquals($responseBuilds->getStatusCode(), 200);
        $contents = $responseBuilds->getBody()->getContents();
        $this->assertNotEmpty($contents);
        $this->assertInternalType('string', $contents);
        $contentsToArray = json_decode($contents, true);
        $this->assertInternalType('array', $contentsToArray);
    }
}
