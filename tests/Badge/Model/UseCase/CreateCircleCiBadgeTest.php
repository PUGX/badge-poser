<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) App\Tests <http://App\Tests.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Badge\Model\UseCase;

use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateCircleCiBadge;
use App\Service\CircleCiClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class CreateCircleCiBadgeTest.
 */
final class CreateCircleCiBadgeTest extends TestCase
{
    /** @var CreateCircleCiBadge $useCase */
    private $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private $repository;
    /** @var CircleCiClientInterface|MockObject */
    private $circleCiClient;

    protected function setUp(): void
    {
        $this->repository = $this->getMockForAbstractClass(PackageRepositoryInterface::class);
        $this->circleCiClient = $this->getMockBuilder(CircleCiClientInterface::class)
            ->setMethods(['getBuilds'])
            ->getMockForAbstractClass();
        $this->useCase = new CreateCircleCiBadge($this->repository, $this->circleCiClient);
    }

    private function createMockWithoutInvokingTheOriginalConstructor(string $classname, array $methods = []): MockObject
    {
        return $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @dataProvider shouldCreateCircleCiBadgeProvider
     *
     * @param string $status
     * @param string $expected
     */
    public function testShouldCreateCircleCiBadge(string $status, string $expected): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode([['status' => $status]]));

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->willReturn($response);

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
            ->willReturn($package);

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );
        $repo->expects($this->once())
            ->method('getRepository')
            ->will($this->throwException(new RuntimeException()));

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->willReturn($repo);

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
            ->willReturn($package);

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(json_encode([]));

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->willReturn($response);

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
            ->willReturn($package);

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

//        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
//            ResponseInterface::class,
//            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
//        );
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
