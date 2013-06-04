<?php

namespace PUGX\BadgeBundle\Tests\Listener;

use PUGX\BadgeBundle\Listener\StatisticListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatisticListenerTest extends WebTestCase
{
    public function setUp()
    {

        $this->persister = $this->getMockBuilder('PUGX\BadgeBundle\Service\Statistic\PersisterInterface')
            ->disableOriginalConstructor()->getMock();

        $this->controllerEvent = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();
    }

    public function testOnKernelController()
    {
        $controller = new \StdClass();
        $method = 'boomAction';
        $repository = 'pugx/badge-poser';

        $this->request->expects($this->once())->method('get')
            ->will($this->returnValue($repository));

        $this->controllerEvent->expects($this->once())->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->controllerEvent->expects($this->once())->method('getController')
            ->will($this->returnValue(array($controller, $method)));

        $this->persister->expects($this->once())->method('incrementTotalAccess')
            ->will($this->returnSelf());
        $this->persister->expects($this->once())->method('incrementRepositoryAccess')
            ->with($repository)
            ->will($this->returnSelf());
        $this->persister->expects($this->once())->method('addRepositoryToLatestAccessed')
            ->will($this->returnSelf());
        $this->persister->expects($this->once())->method('incrementRepositoryAccessType')
            ->with($repository, $method)
            ->will($this->returnSelf());

        $this->listener = new StatisticListener($this->persister);
        $this->listener->onKernelController($this->controllerEvent);

    }
}