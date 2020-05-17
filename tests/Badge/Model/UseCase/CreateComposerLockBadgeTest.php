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

use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateComposerLockBadge;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class CreateComposerLockBadgeTest.
 */
final class CreateComposerLockBadgeTest extends TestCase
{
    private CreateComposerLockBadge $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private $repository;
    /** @var ClientInterface|MockObject */
    private $client;

    protected function setUp(): void
    {
        $this->repository = $this->getMockForAbstractClass(PackageRepositoryInterface::class);
        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['request'])
            ->getMockForAbstractClass();
        $this->useCase = new CreateComposerLockBadge($this->repository, $this->client);
    }

    private function createMockWithoutInvokingTheOriginalConstructor(string $classname, array $methods = [])
    {
        return $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @dataProvider shouldCreateComposerLockBadgeProvider
     */
    public function testShouldCreateComposerLockBadge($returnCode, $expected): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\App\Badge\Model\Package',
            ['hasStableVersion', 'getLatestStableVersion', 'getOriginalObject', 'getDefaultBranch']
        );

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\Packagist\Api\Result\Package',
            ['getRepository']
        );
        $repo->expects($this->once())
            ->method('getRepository')
            ->willReturn('RepoURI');

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->willReturn($repo);

        $package->expects($this->once())
            ->method('getDefaultBranch')
            ->willReturn('master');

        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\Psr\Http\Message\ResponseInterface',
            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
        );
        $response->expects($this->once())
            ->method('getStatusCode')
            ->willReturn($returnCode);

        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($response);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createComposerLockBadge($repository);
        $this->assertEquals($expected, $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\App\Badge\Model\Package',
            ['hasStableVersion', 'getLatestStableVersion', 'getOriginalObject']
        );

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\Packagist\Api\Result\Package',
            ['getRepository']
        );
        $repo->expects($this->once())
            ->method('getRepository')
            ->will($this->throwException(new RuntimeException()));

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->willReturn($repo);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createComposerLockBadge($repository);

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }

    public function shouldCreateComposerLockBadgeProvider(): array
    {
        return [
            [200, 'committed'],
            [404, 'uncommitted'],
            [500, 'checking'],
        ];
    }
}
