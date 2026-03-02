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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class CreateCircleCiBadgeTest extends TestCase
{
    private CreateCircleCiBadge $useCase;
    private MockObject|PackageRepositoryInterface $repository;
    private MockObject|CircleCiClientInterface $circleCiClient;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->circleCiClient = $this->createMock(CircleCiClientInterface::class);
        $this->useCase = new CreateCircleCiBadge($this->repository, $this->circleCiClient);
    }

    /**
     * @param array<int, mixed> $methods
     */
    private function createMockWithoutInvokingTheOriginalConstructor(string $classname, array $methods = []): MockObject
    {
        return $this->createMock($classname);
    }

    #[DataProvider('shouldCreateCircleCiBadgeProvider')]
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

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(\json_encode([['status' => $status]]));

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);
        self::assertEquals($expected, $badge->getStatus());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function shouldCreateCircleCiBadgeProvider(): array
    {
        return [
            ['success', 'passing'],
            ['fail', 'failing'],
        ];
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(Package::class);

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $package->expects($this->once())
            ->method('getRepository')
            ->willThrowException(new \RuntimeException());

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

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);

        $response->expects($this->once())
            ->method('getContent')
            ->willReturn(\json_encode([]));

        $this->circleCiClient->expects($this->once())
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

        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $this->circleCiClient->expects($this->once())
            ->method('getBuilds')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createCircleCiBadge($repository);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
