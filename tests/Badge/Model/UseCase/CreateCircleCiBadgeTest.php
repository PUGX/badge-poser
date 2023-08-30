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
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CreateCircleCiBadgeTest extends TestCase
{
    private CreateCircleCiBadge $useCase;
    /** @var MockObject|PackageRepositoryInterface */
    private MockObject $repository;
    /** @var MockObject|CircleCiClientInterface */
    private MockObject $circleCiClient;

    protected function setUp(): void
    {
        $this->repository = $this->getMockForAbstractClass(PackageRepositoryInterface::class);
        $this->circleCiClient = $this->getMockBuilder(CircleCiClientInterface::class)
            ->setMethods(['getBuilds'])
            ->getMockForAbstractClass();
        $this->useCase = new CreateCircleCiBadge($this->repository, $this->circleCiClient);
    }

    /**
     * @param array<int, mixed> $methods
     */
    private function createMockWithoutInvokingTheOriginalConstructor(string $classname, array $methods = []): MockObject
    {
        return $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @dataProvider shouldCreateCircleCiBadgeProvider
     */
    public function testShouldCreateCircleCiBadge(string $status, string $expected): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects(self::once())
            ->method('getContent')
            ->willReturn(\json_encode([['status' => $status]]));

        $this->circleCiClient->expects(self::once())
            ->method('getBuilds')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);
        self::assertEquals($expected, $badge->getStatus());
    }

    /**
     * @return array<int, array<int, string>>
     */
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

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );
        $repo->expects(self::once())
            ->method('getRepository')
            ->will(self::throwException(new \RuntimeException()));

        $package->expects(self::once())
            ->method('getOriginalObject')
            ->willReturn($repo);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }

    public function testShouldCreateDefaultBadgeWhenNoResults(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $this->createMockWithoutInvokingTheOriginalConstructor(
            \Packagist\Api\Result\Package::class,
            ['getRepository']
        );

        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()->getMock();

        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects(self::once())
            ->method('getContent')
            ->willReturn(\json_encode([]));

        $this->circleCiClient->expects(self::once())
            ->method('getBuilds')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }

    public function testShouldCreateDefaultBadgeWhenCircleConfigNotExist(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository
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

        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn(404);

        $this->circleCiClient->expects(self::once())
            ->method('getBuilds')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
