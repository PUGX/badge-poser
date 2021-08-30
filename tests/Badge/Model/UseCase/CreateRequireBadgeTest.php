<?php

namespace App\Tests\Badge\Model\UseCase;

use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\UseCase\CreateRequireBadge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CreateRequireBadgeTest extends TestCase
{
    private CreateRequireBadge $useCase;
    /** @var PackageRepositoryInterface|MockObject */
    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(PackageRepositoryInterface::class);
        $this->useCase = new CreateRequireBadge($this->repository);
    }

    public function testShouldCreateRequireBadge(): void
    {
        $package = $this->getMockBuilder(Package::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLatestRequire'])
            ->getMock();

        $package->expects(self::once())
            ->method('getLatestRequire')
            ->willReturn('^8.0');

        $this->repository
            ->method('fetchByRepository')
            ->willReturn($package);

        $repository = 'PUGX/badge-poser';
        $type = 'php';
        $badge = $this->useCase->createRequireBadge($repository, $type);
        self::assertEquals($type, $badge->getSubject());
        self::assertEquals('^8.0', $badge->getStatus());
        self::assertEquals('#787CB5', $badge->getHexColor());
    }

    public function testShouldCreateDefaultBadgeOnError(): void
    {
        $this->repository
            ->method('fetchByRepository')
            ->will(self::throwException(new \RuntimeException()));

        $repository = 'PUGX/badge-poser';
        $type = 'php';
        $badge = $this->useCase->createRequireBadge($repository, $type);

        self::assertEquals(' - ', $badge->getSubject());
        self::assertEquals(' - ', $badge->getStatus());
        self::assertEquals('#7A7A7A', $badge->getHexColor());
    }
}
