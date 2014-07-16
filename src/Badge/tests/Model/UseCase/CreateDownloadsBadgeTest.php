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

use PUGX\Badge\Model\Package;
use PUGX\Badge\Model\UseCase\CreateDownloadsBadge;

/**
 * Class DownloadsImageCreatorTest
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CreateDownloadsBadgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var $useCase */
    private $useCase;
    /** @var PUGX\Badge\Model\PackageRepositoryInterface*/
    private $repository;

    public function setUp()
    {
        $this->repository = $this->getMock('\PUGX\Badge\Model\PackageRepositoryInterface');
        $this->useCase = new CreateDownloadsBadge($this->repository);
    }

    public function testShouldCreateDownloadsBadge()
    {
        $package = $this->getMockBuilder('\PUGX\Badge\Model\Package')
            ->disableOriginalConstructor()
            ->getMock();

        $package->expects($this->once())
            ->method('getPackageDownloads')
            ->will($this->returnValue(102));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repository = 'PUGX/badge-poser';

        $badge = $this->useCase->createDownloadsBadge($repository, 'daily', 'svg');

        $this->assertEquals('102 today', $badge->getStatus());

    }
}
