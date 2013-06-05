<?php

namespace PUGX\StatsBundle\Tests\Listener;

use PUGX\StatsBundle\Listener\StatsListener;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatListenerTest extends WebTestCase
{
    protected $persister;

    protected $controllerEvent;

    protected $request;

    protected $listener;

    public function setUp()
    {
        $this->persister = $this->getMockBuilder('PUGX\StatsBundle\Service\PersisterInterface')
            ->disableOriginalConstructor()->getMock();

        $this->controllerEvent = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterControllerEvent')
            ->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()->getMock();

        $this->listener = new StatsListener($this->persister);
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

        $this->listener->onKernelController($this->controllerEvent);
    }
}