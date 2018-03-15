<?php

namespace App\Tests\EventListener;

use App\EventListener\StatsSubscriber;
use App\Stats\Persister\PersisterInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class StatsSubscriberTest.
 */
final class StatsSubscriberTest extends WebTestCase
{
    protected $persister;

    protected $controllerEvent;

    protected $request;

    protected $listener;

    protected function setUp(): void
    {
        $this->persister = $this->getMockBuilder(PersisterInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->controllerEvent = $this->getMockBuilder(FilterControllerEvent::class)
            ->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()->getMock();

        $this->listener = new StatsSubscriber($this->persister);
    }

    public function testOnKernelController(): void
    {
        $controller = new \StdClass();
        $method = 'boomAction';
        $repository = 'pugx/badge-poser';
        $url = 'https://poser.pugx.org';
        $this->request->expects($this->at(0))
            ->method('get')
            ->with('repository')
            ->will($this->returnValue($repository));
        $this->request->expects($this->at(1))
            ->method('get')
            ->with('_route')
            ->will($this->returnValue('route_xyz'));

        // adding referer
        $this->request->headers = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->headers->expects($this->once())
            ->method('get')
            ->with('referer')
            ->will($this->returnValue($url));

        $this->controllerEvent->expects($this->once())->method('getRequest')
            ->will($this->returnValue($this->request));
        $this->controllerEvent->expects($this->once())->method('getController')
            ->will($this->returnValue([$controller, $method]));

        $this->persister
            ->expects($this->once())
            ->method('incrementTotalAccess')
            ->will($this->returnSelf());

        $this->persister
            ->expects($this->once())
            ->method('incrementRepositoryAccess')
            ->with($repository)
            ->will($this->returnSelf());

        $this->persister
            ->expects($this->once())
            ->method('addRepositoryToLatestAccessed')
            ->will($this->returnSelf());

        $this->persister
            ->expects($this->once())
            ->method('incrementRepositoryAccessType')
            ->with($repository, $method)
            ->will($this->returnSelf());

        $this->persister
            ->expects($this->once())
            ->method('addReferer')
            ->with($url)
            ->will($this->returnSelf());

        $this->listener->onKernelController($this->controllerEvent);
    }
}
