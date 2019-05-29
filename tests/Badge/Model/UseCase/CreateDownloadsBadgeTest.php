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

use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateDownloadsBadge;
use PHPUnit\Framework\TestCase;

/**
 * Class CreateDownloadsBadgeTest.
 */
class CreateDownloadsBadgeTest extends TestCase
{
    /** @var CreateDownloadsBadge */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateDownloadsBadge($this->repository);
    }

    public function testShouldCreateDownloadsBadge(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPackageDownloads'])
            ->getMock();

        $package->expects($this->once())
            ->method('getPackageDownloads')
            ->willReturn(102);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $repository = 'PUGX/badge-poser';

        $badge = $this->useCase->createDownloadsBadge($repository, 'daily', 'svg');

        $this->assertEquals('102 today', $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->throwException(new \RuntimeException()));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createDownloadsBadge($repository, 'daily', 'svg');

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
