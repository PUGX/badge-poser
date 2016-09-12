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
use PUGX\Badge\Model\UseCase\CreateComposerLockBadge;

/**
 * Class LicenseImageCreatorTest
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class CreateComposerLockBadgeTest extends \PHPUnit_Framework_TestCase
{
    /** @var CreateComposerLockBadge $useCase */
    private $useCase;
    /** @var \PUGX\Badge\Model\PackageRepositoryInterface */
    private $repository;

    private $client;

    public function setUp()
    {
        $this->repository = $this->getMock('\PUGX\Badge\Model\PackageRepositoryInterface');
        $this->client = $this->getMock('\Guzzle\Http\Client');
        $this->useCase = new CreateComposerLockBadge($this->repository, $this->client);
    }

    /**
     * @dataProvider shouldCreateComposerLockBadgeProvider
     */
    public function testShouldCreateComposerLockBadge($returnCode, $expected)
    {
        $package = $this->getMockWithoutInvokingTheOriginalConstructor('\PUGX\Badge\Model\Package');

        $package->expects($this->once())
            ->method('hasStableVersion')
            ->will($this->returnValue(true));

        $package->expects($this->once())
            ->method('getLatestStableVersion')
            ->will($this->returnValue('v2.0'));

        $this->repository->expects($this->any())
            ->method('fetchByRepository')
            ->will($this->returnValue($package));

        $repo = $this->getMockWithoutInvokingTheOriginalConstructor('\Packagist\Api\Result\Package');
        $repo->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue('RepoURI'));

        $package->expects($this->once())
            ->method('getOriginalObject')
            ->will($this->returnValue($repo));

        $response = $this->getMockWithoutInvokingTheOriginalConstructor('\Guzzle\Http\Message\Response');
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue($returnCode));

        $request = $this->getMockWithoutInvokingTheOriginalConstructor('\Guzzle\Http\Message\RequestInterface');

        $this->client->expects($this->once())
            ->method('head')
            ->will($this->returnValue($request));

        $this->client->expects($this->once())
            ->method('send')
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
