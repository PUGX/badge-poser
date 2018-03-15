<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) App\Tests <http://App\Tests.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Badge\UseCase;

use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateCircleCiBadge;
use App\Service\CircleCiClientInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use App\Badge\Model\Package;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CreateCircleCiBadgeTest
 * @package App\Tests\Badge\UseCase
 */
class CreateCircleCiBadgeTest extends TestCase
{
    /** @var CreateCircleCiBadge $useCase */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;
    /** @var CircleCiClientInterface */
    private $circleCiClient;

    public function setUp()
    {
        $this->repository = $this->getMockForAbstractClass(PackageRepositoryInterface::class);
        $this->circleCiClient = $this->getMockBuilder(CircleCiClientInterface::class)
            ->setMethods(['getBuilds'])
            ->getMockForAbstractClass();
        $this->useCase = new CreateCircleCiBadge($this->repository, $this->circleCiClient);
    }

    private function createMockWithoutInvokingTheOriginalConstructor(string $classname, array $methods = [])
    {
        return $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @dataProvider shouldCreateCircleCiBadgeProvider
     * @param $status
     * @param $expected
     */
    public function testShouldCreateCircleCiBadge($status, $expected): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
            ResponseInterface::class,
            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
        );

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $responseBody = $this->getMockBuilder(StreamInterface::class)
            ->getMockForAbstractClass();

        $responseBody->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue(json_encode([['status' => $status]])));

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($responseBody));

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->will($this->returnValue($response));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);
        $this->assertEquals($expected, $badge->getStatus());
    }

    public function shouldCreateCircleCiBadgeProvider(): array
    {
        return [
            ['success', 'passing'],
            ['fail', 'failing'],
        ];
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(
            Package::class,
            ['hasStableVersion', 'getLatestStableVersion', 'getOriginalObject']
        );

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );
        $repo->expects($this->once())
            ->method('getRepository')
            ->will($this->throwException(new RuntimeException()));

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->will($this->returnValue($repo));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }

    public function testShouldCreateDefaultBadgeWhenNoResults(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
            ResponseInterface::class,
            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
        );

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));

        $responseBody = $this->getMockBuilder(StreamInterface::class)
            ->getMockForAbstractClass();

        $responseBody->expects($this->once())
            ->method('getContents')
            ->will($this->returnValue(json_encode([])));

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($responseBody));

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->will($this->returnValue($response));


        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }

    public function testShouldCreateDefaultBadgeWhenCircleConfigNotExist(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
            ResponseInterface::class,
            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
        );

        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(404));

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->will($this->returnValue($response));


        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
