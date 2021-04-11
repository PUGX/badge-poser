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
use App\Badge\Model\UseCase\CreateLicenseBadge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateLicenseBadgeTest extends TestCase
{
    private CreateLicenseBadge $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateLicenseBadge($this->repository);
    }

    public function testShouldCreateLicenseBadge(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLicense'])
            ->getMock();

        $package->expects(self::once())
            ->method('getLicense')
            ->willReturn('MIT');

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createLicenseBadge($repository);
        self::assertEquals('MIT', $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $this->repository
            ->method('fetchByRepository')
            ->will(self::throwException(new \RuntimeException()));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createLicenseBadge($repository);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
