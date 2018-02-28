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

use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateVersionBadge;
use PHPUnit\Framework\TestCase;

/**
 * Class LicenseImageCreatorTest
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CreateVersionBadgeTest extends TestCase
{
    /** @var $useCase */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;

    public function setUp()
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateVersionBadge($this->repository);
    }

    public function testShouldCreateLicenseBadge()
    {
        $package = $this->getMockBuilder('\App\Badge\Model\Package')
            ->disableOriginalConstructor()
            ->getMock();

        $package->expects($this->once())
            ->method('hasStableVersion')
            ->will($this->returnValue(true));

        $package->expects($this->once())
            ->method('getLatestStableVersion')
            ->will($this->returnValue('v2.0'));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repository = 'App\Tests/badge-poser';
        $badge = $this->useCase->createStableBadge($package);
        $this->assertEquals('v2.0', $badge->getStatus());
    }
}
