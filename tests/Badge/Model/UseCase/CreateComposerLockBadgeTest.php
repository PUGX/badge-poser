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
use App\Badge\Service\ClientStrategy;
use App\Badge\ValueObject\Repository;
use GuzzleHttp\ClientInterface;
use Packagist\Api\Result\Package;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;

final class CreateComposerLockBadgeTest extends TestCase
{
    use ProphecyTrait;
    private CreateComposerLockBadge $useCase;
    /** @var PackageRepositoryInterface&MockObject */
    private MockObject $repository;
    /** @var ClientInterface&MockObject */
    private MockObject $client;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->client = $this->createMock(ClientInterface::class);
        $clientStrategy = $this->prophesize(ClientStrategy::class);

        $repoUrl = 'https://github.com/user/repository';
        $repositoryInfo = Repository::createFromRepositoryUrl($repoUrl);
        $clientStrategy->getRepositoryPrefix($repositoryInfo, $repoUrl)
            ->willReturn('');
        $this->useCase = new CreateComposerLockBadge($this->repository, $this->client, $clientStrategy->reveal());
    }

    #[DataProvider('shouldCreateComposerLockBadgeProvider')]
    public function testShouldCreateComposerLockBadge(int $returnCode, string $expected): void
    {
        $package = $this->createMock(AppPackage::class);

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMock(Package::class);
        $repo->expects($this->once())
            ->method('getRepository')
            ->willReturn('https://github.com/user/repository');

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->willReturn($repo);

        $package->expects($this->once())
            ->method('getDefaultBranch')
            ->willReturn('master');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($returnCode);

        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createComposerLockBadge($repository);
        self::assertEquals($expected, $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $package = $this->createMock(AppPackage::class);

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMock(Package::class);
        $repo->expects($this->once())
            ->method('getRepository')
            ->will($this->throwException(new \RuntimeException()));

        $package->expects($this->once())
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
    public static function shouldCreateComposerLockBadgeProvider(): array
    {
        return [
            [200, 'committed'],
            [404, 'uncommitted'],
            [500, 'checking'],
        ];
    }
}
