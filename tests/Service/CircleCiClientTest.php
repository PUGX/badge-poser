<?php

namespace App\Tests\Service;

use App\Service\CircleCiClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CircleCiClientTest extends TestCase
{
    /** @var UrlGeneratorInterface|MockObject */
    protected $router;

    /** @var HttpClientInterface|MockObject */
    protected $httpClient;

    /** @var CircleCiClient */
    protected $circleCiClient;

    protected function setUp(): void
    {
        $this->router = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->router->expects($this->any())
            ->method('generate')
            ->willReturn('fake-url-circleci-api');

        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->circleCiClient = new CircleCiClient($this->router, $this->httpClient, 'fake-token');
    }

    public function testGetBuilds(): void
    {
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response->expects($this->any())
            ->method('getContent')
            ->willReturn(json_encode([['status' => 'success']]));

        $response->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->httpClient->expects($this->any())
            ->method('request')
            ->willReturn($response);

        $responseBuilds = $this->circleCiClient->getBuilds('pugx/badge-poser');

        $this->assertInstanceOf(ResponseInterface::class, $responseBuilds);
        $this->assertEquals($responseBuilds->getStatusCode(), 200);
        $content = $responseBuilds->getContent();
        $this->assertNotEmpty($content);
        $this->assertInternalType('string', $content);
        $contentToArray = json_decode($content, true);
        $this->assertInternalType('array', $contentToArray);
    }
}
