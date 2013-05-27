<?php

namespace PUGX\BadgeBundle\Tests\Service;

use Packagist\Api\Result\Package\Downloads;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use PUGX\BadgeBundle\Service\Badger;


class BadgerTest extends WebTestCase
{

    private $logger;
    private $dispatcher;
    private $packagistClient;

    public function setUp() {
        $this->logger = $this->getMockBuilder('Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->dispatcher->expects($this->once())
            ->method('dispatch');

        $this->packagistClient = $this->getMock('Packagist\Api\Client');
    }


    public function testGetPackageDownloads()
    {
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



}
