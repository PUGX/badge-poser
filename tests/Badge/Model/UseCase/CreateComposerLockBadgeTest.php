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

use App\Badge\Package\Package;
use App\Badge\Model\UseCase\CreateComposerLockBadge;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;

/**
 * Class LicenseImageCreatorTest
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CreateComposerLockBadgeTest extends TestCase
{
    /** @var CreateComposerLockBadge $useCase */
    private $useCase;
    /** @var \App\Tests\Badge\Model\PackageRepositoryInterface */
    private $repository;

    private $client;

    public function setUp()
    {
        $this->repository = $this->getMockForAbstractClass('\App\Badge\Model\PackageRepositoryInterface');
        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['head', 'send'])
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
            '\Guzzle\Http\Message\Response',
            ['getStatusCode']
        );
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($returnCode));

        $request = $this->createMockWithoutInvokingTheOriginalConstructor('Psr\Http\Message\RequestInterface');

        $this->client->expects($this->once())
            ->method('head')
            ->will($this->returnValue($request));

        $this->client->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $repository = 'App\Tests/badge-poser';
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
