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
use App\Badge\Model\UseCase\CreateDependentsBadge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class DependentsImageCreatorTest.
 */
final class CreateDependentsBadgeTest extends TestCase
{
    private CreateDependentsBadge $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateDependentsBadge($this->repository);
    }

    public function testShouldCreateDependentsBadge(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDependents'])
            ->getMock();

        $package->expects($this->once())
            ->method('getDependents')
            ->willReturn(1);

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->willReturn($package);

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createDependentsBadge($repository)->getBadge();
        $this->assertEquals(CreateDependentsBadge::SUBJECT, $badge->getSubject());
        $this->assertEquals('#'.CreateDependentsBadge::COLOR, $badge->getHexColor());
        $this->assertEquals('1', $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->throwException(new \RuntimeException()));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createDependentsBadge($repository)->getBadge();

        $this->assertEquals(' - ', $badge->getSubject());
        $this->assertEquals(' - ', $badge->getStatus());
        $this->assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
