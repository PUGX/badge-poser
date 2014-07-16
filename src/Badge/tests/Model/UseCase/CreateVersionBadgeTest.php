<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\UseCase;

use PUGX\Badge\Package\Package;
use PUGX\Badge\Model\UseCase\CreateVersionBadge;

/**
 * Class LicenseImageCreatorTest
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CreateVersionBadgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var $useCase */
    private $useCase;
    /** @var PUGX\Badge\Model\PackageRepositoryInterface*/
    private $repository;

    public function setUp()
    {
        $this->repository = $this->getMock('\PUGX\Badge\Model\PackageRepositoryInterface');
        $this->useCase = new CreateVersionBadge($this->repository);
    }

    public function testShouldCreateLicenseBadge()
    {
        $package = $this->getMockBuilder('\PUGX\Badge\Model\Package')
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

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createStableBadge($package);
        $this->assertEquals('v2.0', $badge->getStatus());
    }
}
