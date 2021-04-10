<?php

namespace App\Tests\Service;

use App\Service\CircleCiClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CircleCiClientTest extends TestCase
{
    /** @var HttpClientInterface|MockObject */
    private MockObject $httpClient;

    private CircleCiClient $circleCiClient;

    protected function setUp(): void
    {
        $router = $this->getMockBuilder(UrlGeneratorInterface::class)
            ->disableOriginalConstructor()->getMock();

        $router
            ->method('generate')
            ->willReturn('fake-url-circleci-api');

        $this->httpClient = $this->getMockBuilder(HttpClientInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->circleCiClient = new CircleCiClient($router, $this->httpClient, 'fake-token');
    }

    public function testGetBuilds(): void
    {
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response
            ->method('getContent')
            ->willReturn(\json_encode([['status' => 'success']]));

        $response
            ->method('getStatusCode')
            ->willReturn(200);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $responseBuilds = $this->circleCiClient->getBuilds('pugx/badge-poser');

        self::assertInstanceOf(ResponseInterface::class, $responseBuilds);
        self::assertEquals($responseBuilds->getStatusCode(), 200);
        $content = $responseBuilds->getContent();
        self::assertNotEmpty($content);
        self::assertIsString($content);
        $contentToArray = \json_decode($content, true);
        self::assertIsArray($contentToArray);
    }
}
