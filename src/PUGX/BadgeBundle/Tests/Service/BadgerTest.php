<?php

/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Tests\Service;

use Packagist\Api\Result\Package\Downloads;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\BadgeBundle\Service\Badger;
use Packagist\Api\Result\Package\Version;

class BadgerTest extends WebTestCase
{

    private $logger;
    private $dispatcher;
    private $packagistClient;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $this->packagistClient = $this->getMock('Packagist\Api\Client');
    }

    public function testGetPackageDownloads()
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $downloads = new Downloads();
        $downloads->setTotal(90000);

        $input = 'pugx/badge-poser';
        $output = $this->getMock('Packagist\Api\Result\Package');
        $output->expects($this->once())
            ->method('getDownloads')
            ->will($this->returnValue($downloads));

        $this->packagistClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo($input))
            ->will($this->returnValue($output));

        $badger = new Badger($this->packagistClient, $this->dispatcher, $this->logger);

        $this->assertEquals($badger->getPackageDownloads($input, 'total'), 90000);
    }

    /**
     * @expectedException Exception
     */
    public function testGetPackageDownloadsException()
    {
        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $input = 'pugx/badge-poser';

        $this->packagistClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo($input))
            ->will($this->throwException(new \Exception));

        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $badger = new Badger($this->packagistClient, $this->dispatcher, $this->logger);

        $this->assertEquals($badger->getPackageDownloads($input, 'total'), null);
    }

    public function testVersionComparison()
    {
        $a = "2.1.10";
        $b = "2.2.1";
        $c = "2.2.2";
        $d = "2.2.2-RC2";

        $this->assertTrue(($b > $a));
        $this->assertTrue(($c > $b));
        $this->assertTrue(($d > $c));
    }

    public function testGetLastStableVersionReturnsLastVersion()
    {
        $branches = array('1.0.0', '1.1.0', '2.0.0', '3.0.x-dev', 'v3.0.0-RC1');
        foreach ($branches as $branch) {
            $version = new Version();
            $version->fromArray(array('version' => $branch));
            $versions[] = $version;
        }

        $input = 'pugx/badge-poser';
        $output = $this->getMock('Packagist\Api\Result\Package');
        $output->expects($this->once())
            ->method('getVersions')
            ->will($this->returnValue($versions));

        $this->packagistClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo($input))
            ->will($this->returnValue($output));

        $badger = new Badger($this->packagistClient, $this->dispatcher, $this->logger);

        $this->assertEquals('2.0.0', $badger->getLastStableVersion($input));
    }

    public function testGetLastStableVersionReturnsNull()
    {
        $branches = array('3.0.x-dev', 'v3.0.0-RC1');
        foreach ($branches as $branch) {
            $version = new Version();
            $version->fromArray(array('version' => $branch));
            $versions[] = $version;
        }

        $input = 'pugx/badge-poser';
        $output = $this->getMock('Packagist\Api\Result\Package');
        $output->expects($this->once())
            ->method('getVersions')
            ->will($this->returnValue($versions));

        $this->packagistClient->expects($this->once())
            ->method('get')
            ->with($this->equalTo($input))
            ->will($this->returnValue($output));

        $badger = new Badger($this->packagistClient, $this->dispatcher, $this->logger);

        $this->assertEquals(null, $badger->getLastStableVersion($input));
    }
}
