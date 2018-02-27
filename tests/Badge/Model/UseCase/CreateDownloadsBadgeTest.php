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
use App\Badge\Model\UseCase\CreateDownloadsBadge;
use PHPUnit\Framework\TestCase;
/**
 * Class DownloadsImageCreatorTest
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CreateDownloadsBadgeTest extends TestCase
{
    /** @var $useCase */
    private $useCase;
    /** @var App\Tests\Badge\Model\PackageRepositoryInterface*/
    private $repository;

    public function setUp()
    {
        $this->repository = $this->createMock('\App\Badge\Model\PackageRepositoryInterface');
        $this->useCase = new CreateDownloadsBadge($this->repository);
    }

    public function testShouldCreateDownloadsBadge()
    {
        $package = $this->getMockBuilder('\App\Tests\Badge\Model\Package')
            ->disableOriginalConstructor()
            ->setMethods(['getPackageDownloads'])
            ->getMock();

        $package->expects($this->once())
            ->method('getPackageDownloads')
            ->will($this->returnValue(102));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repository = 'App\Tests/badge-poser';

        $badge = $this->useCase->createDownloadsBadge($repository, 'daily', 'svg');

        $this->assertEquals('102 today', $badge->getStatus());

    }
}
