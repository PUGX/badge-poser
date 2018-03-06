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
use App\Badge\Model\UseCase\CreateComposerLockBadge;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;

/**
 * Class LicenseImageCreatorTest
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Andrea Giannantonio <a.giannantonio@gmail.com>
 */
class CreateComposerLockBadgeTest extends TestCase
{
    /** @var CreateComposerLockBadge $useCase */
    private $useCase;
    /** @var PackageRepositoryInterface */
    private $repository;
    /** @var ClientInterface */
    private $client;

    public function setUp()
    {
        $this->repository = $this->getMockForAbstractClass(PackageRepositoryInterface::class);
        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['request'])
            ->getMockForAbstractClass();
        $this->useCase = new CreateComposerLockBadge($this->repository, $this->client);
    }

    private function createMockWithoutInvokingTheOriginalConstructor(string $classname, array $methods = [])
    {
        return $this->getMockBuilder($classname)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @dataProvider shouldCreateComposerLockBadgeProvider
     */
    public function testShouldCreateComposerLockBadge($returnCode, $expected)
    {
        $package = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\App\Badge\Model\Package', 
            ['hasStableVersion', 'getLatestStableVersion', 'getOriginalObject']
        );

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repo = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\Packagist\Api\Result\Package',
            ['getRepository']
        );
        $repo->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue('RepoURI'));

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->will($this->returnValue($repo));

        $response = $this->createMockWithoutInvokingTheOriginalConstructor(
            '\Psr\Http\Message\ResponseInterface',
            ['getStatusCode', 'withStatus', 'getReasonPhrase', 'getProtocolVersion', 'withProtocolVersion', 'getHeaders', 'hasHeader', 'getHeader', 'getHeaderLine', 'withHeader', 'withAddedHeader', 'withoutHeader', 'getBody', 'withBody']
        );
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($returnCode));

        $this->client->expects($this->once())
            ->method('request')
            ->will($this->returnValue($response));

        $repository = 'PUGX/badge-poser';
        $badge = $this->useCase->createComposerLockBadge($repository);
        $this->assertEquals($expected, $badge->getStatus());
    }

    public function shouldCreateComposerLockBadgeProvider()
    {
        return array(
            array(200, 'committed'),
            array(404, 'uncommitted'),
            array(500, 'checking'),
        );
    }
}
