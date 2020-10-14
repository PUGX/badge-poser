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

use App\Badge\Model\Badge;
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateVersionBadge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateVersionBadgeTest.
 */
final class CreateVersionBadgeTest extends TestCase
{
    private CreateVersionBadge $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateVersionBadge($this->repository);
    }

    public function testShouldCreateLicenseBadge(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $package->expects($this->once())
            ->method('getLatestStableVersion')
            ->willReturn('v2.0');

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $badge = $this->useCase->createStableBadge('PUGX/badge-poser');

        $this->assertEquals(new Badge('stable', 'v2.0', '28a3df'), $badge);
    }

    public function testShouldCreateNoStableReleaseBadgeWhenNoStableVersionAvailable(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $package->method('getLatestStableVersion')
            ->willReturn(null);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $badge = $this->useCase->createStableBadge('PUGX/badge-poser');

        $this->assertEquals(new Badge('stable', 'No Release', '28a3df'), $badge);
    }

    public function testShouldCreateDefaultBadgeOnErrorWhenCreatingLicenseBadge(): void
    {
        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->throwException(new \RuntimeException()));

        $badge = $this->useCase->createStableBadge('PUGX/badge-poser');

        $this->assertEquals(new Badge(' - ', ' - ', '7A7A7A'), $badge);
    }

    public function testShouldCreateUnstableBadge(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $package->expects($this->once())
            ->method('getLatestUnstableVersion')
            ->willReturn('v2.0');

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $badge = $this->useCase->createUnstableBadge('PUGX/badge-poser');

        $this->assertEquals(new Badge('unstable', 'v2.0', 'e68718'), $badge);
    }

    public function testShouldCreateNoUnstableReleaseBadgeWhenNoUnstableVersionAvailable(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->getMock();

        $package->method('getLatestUnstableVersion')
            ->willReturn(null);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $badge = $this->useCase->createUnstableBadge('PUGX/badge-poser');

        $this->assertEquals(new Badge('unstable', 'No Release', 'e68718'), $badge);
    }

    public function testShouldCreateDefaultBadgeOnErrorWhenCreatingUnstableReleaseBadge(): void
    {
        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->throwException(new \RuntimeException()));

        $badge = $this->useCase->createUnstableBadge('PUGX/badge-poser');

        $this->assertEquals(new Badge(' - ', ' - ', '7A7A7A'), $badge);
    }
}
