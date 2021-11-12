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

use App\Badge\Model\Package as AppPackage;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateComposerLockBadge;
use App\Badge\Service\ClientStrategyInterface;
use GuzzleHttp\ClientInterface;
use Packagist\Api\Result\Package;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

final class CreateComposerLockBadgeTest extends TestCase
{
    private CreateComposerLockBadge $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private MockObject $repository;
    /** @var ClientInterface|MockObject */
    private MockObject $client;
    /** @var ClientStrategyInterface|MockObject */
    private $clientStrategy;

    protected function setUp(): void
    {
        $this->repository = $this->getMockForAbstractClass(PackageRepositoryInterface::class);
        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['request'])
            ->getMockForAbstractClass();
        $this->clientStrategy = $this->createMock(ClientStrategyInterface::class);
        $this->useCase = new CreateComposerLockBadge($this->repository, $this->client, $this->clientStrategy);
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
     * @dataProvider shouldCreateComposerLockBadgeProvider
     */
    public function testShouldCreateComposerLockBadge(int $returnCode, string $expected): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(
            AppPackage::class,
            ['hasStableVersion', 'getLatestStableVersion', 'getOriginalObject', 'getDefaultBranch']
        );

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            Package::class,
            ['getRepository']
        );
        $repo->expects(self::once())
            ->method('getRepository')
            ->willReturn('https://github.com/user/repository');

        $package->expects(self::once())
            ->method('getOriginalObject')
            ->willReturn($repo);

        $package->expects(self::once())
            ->method('getDefaultBranch')
            ->willReturn('master');

        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
            ResponseInterface::class,
            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
        );
        $response->expects(self::once())
            ->method('getStatusCode')
            ->willReturn($returnCode);

        $this->client->expects(self::once())
            ->method('request')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createComposerLockBadge($repository);
        self::assertEquals($expected, $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(
            AppPackage::class,
            ['hasStableVersion', 'getLatestStableVersion', 'getOriginalObject']
        );

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            Package::class,
            ['getRepository']
        );
        $repo->expects(self::once())
            ->method('getRepository')
            ->will(self::throwException(new RuntimeException()));

        $package->expects(self::once())
            ->method('getOriginalObject')
            ->willReturn($repo);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createComposerLockBadge($repository);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }

    /**
     * @return array<int, array<int, int|string>>
     */
    public function shouldCreateComposerLockBadgeProvider(): array
    {
        return [
            [200, 'committed'],
            [404, 'uncommitted'],
            [500, 'checking'],
        ];
    }
}
