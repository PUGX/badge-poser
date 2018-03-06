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
use App\Badge\Model\UseCase\CreateLicenseBadge;
use PHPUnit\Framework\TestCase;

/**
 * Class LicenseImageCreatorTest
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Andrea Giannantonio <a.giannantonio@gmail.com>
 */
class CreateLicenseBadgeTest extends TestCase
{
    /** @var CreateLicenseBadge */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;

    public function setUp()
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateLicenseBadge($this->repository);
    }

    public function testShouldCreateLicenseBadge()
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLicense'])
            ->getMock();

        $package->expects($this->once())
            ->method('getLicense')
            ->will($this->returnValue('MIT'));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createLicenseBadge($repository);
        $this->assertEquals('MIT', $badge->getStatus());
    }
}
