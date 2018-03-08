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
use App\Badge\Model\UseCase\CreateSuggestersBadge;
use PHPUnit\Framework\TestCase;

/**
 * Class SuggestersImageCreatorTest
 */
class CreateSuggestersBadgeTest extends TestCase
{
    /** @var CreateSuggestersBadge */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;

    public function setUp()
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateSuggestersBadge($this->repository);
    }

    public function testShouldCreateSuggestersBadge()
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSuggesters'])
            ->getMock();

        $package->expects($this->once())
            ->method('getSuggesters')
            ->will($this->returnValue(3));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createSuggestersBadge($repository);
        $this->assertEquals(CreateSuggestersBadge::SUBJECT, $badge->getSubject());
        $this->assertEquals(CreateSuggestersBadge::COLOR, $badge->getHexColor());
        $this->assertEquals('3', $badge->getStatus());
    }

    public function testShouldCreateDefaultBadgeOnError()
    {
        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->throwException(new \RuntimeException()));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createSuggestersBadge($repository);

        $this->assertEquals(CreateSuggestersBadge::SUBJECT, $badge->getSubject());
        $this->assertEquals(CreateSuggestersBadge::COLOR, $badge->getHexColor());
        $this->assertEquals('0', $badge->getStatus());
    }
}
