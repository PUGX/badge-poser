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
use App\Badge\Model\UseCase\CreateDependentsBadge;
use PHPUnit\Framework\TestCase;

/**
 * Class DependentsImageCreatorTest
 */
class CreateDependentsBadgeTest extends TestCase
{
    /** @var CreateDependentsBadge */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;

    public function setUp()
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateDependentsBadge($this->repository);
    }

    public function testShouldCreateDependentsBadge()
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDependents'])
            ->getMock();

        $package->expects($this->once())
            ->method('getDependents')
            ->will($this->returnValue(1));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createDependentsBadge($repository);
        $this->assertEquals(CreateDependentsBadge::SUBJECT, $badge->getSubject());
        $this->assertEquals(CreateDependentsBadge::COLOR, $badge->getHexColor());
        $this->assertEquals('1', $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError()
    {
        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->throwException(new \RuntimeException()));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createDependentsBadge($repository);

        $this->assertEquals(CreateDependentsBadge::SUBJECT, $badge->getSubject());
        $this->assertEquals(CreateDependentsBadge::COLOR, $badge->getHexColor());
        $this->assertEquals('0', $badge->getStatus());
    }
}
