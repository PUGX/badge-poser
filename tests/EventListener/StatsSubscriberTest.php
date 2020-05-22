<?php

namespace App\Tests\EventListener;

use App\EventListener\StatsSubscriber;
use App\Stats\Persister\PersisterInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class StatsSubscriberTest.
 */
final class StatsSubscriberTest extends WebTestCase
{
    /** @var PersisterInterface&\PHPUnit\Framework\MockObject\MockObject */
    protected $persister;

    /** @var \PHPUnit\Framework\MockObject\MockObject&ControllerEvent */
    protected $controllerEvent;

    /** @var \PHPUnit\Framework\MockObject\MockObject&Request */
    protected $request;

    private StatsSubscriber $listener;

    protected function setUp(): void
    {
        $this->persister = $this->getMockBuilder(PersisterInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->controllerEvent = $this->getMockBuilder(ControllerEvent::class)
            ->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()->getMock();

        $this->listener = new StatsSubscriber($this->persister);
    }

    public function testOnKernelController(): void
    {
        $controller = new StdClass();
        $method = 'boomAction';
        $repository = 'pugx/badge-poser';
        $url = 'https://poser.pugx.org';
        $actualPage = 'https://poser.pugx.org/badges';
        $this->request->expects($this->at(0))
            ->method('get')
            ->with('repository')
            ->willReturn($repository);
        $this->request->expects($this->at(1))
            ->method('get')
            ->with('_route')
            ->willReturn('route_xyz');
        $this->request->expects($this->once())
            ->method('getSchemeAndHttpHost')
            ->willReturn($actualPage);

        // adding referer
        /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\HttpFoundation\HeaderBag<mixed> $headers */
        $headers = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $headers->expects($this->once())
            ->method('get')
            ->with('referer')
            ->willReturn($url);
        $this->request->headers = $headers;

        $this->controllerEvent->expects($this->once())->method('getRequest')
            ->willReturn($this->request);
        $this->controllerEvent->expects($this->once())->method('getController')
            ->willReturn([$controller, $method]);

        $this->persister
            ->expects($this->once())
            ->method('incrementTotalAccess')
            ->willReturnSelf();

        $this->persister
            ->expects($this->once())
            ->method('incrementRepositoryAccess')
            ->with($repository)
            ->willReturnSelf();

        $this->persister
            ->expects($this->once())
            ->method('addRepositoryToLatestAccessed')
            ->willReturnSelf();

        $this->persister
            ->expects($this->once())
            ->method('incrementRepositoryAccessType')
            ->with($repository, $method)
            ->willReturnSelf();

        $this->persister
            ->expects($this->once())
            ->method('addReferer')
            ->with($url)
            ->willReturnSelf();

        $this->listener->onKernelController($this->controllerEvent);
    }

    public function testWhenIsARefreshShouldNotIncrement(): void
    {
        $controller = new StdClass();
        $method = 'boomAction';
        $repository = 'pugx/badge-poser';
        $url = 'https://poser.pugx.org/show/phpunit/phpunit/';
        $actualPage = 'https://poser.pugx.org/show/phpunit/phpunit';
        $this->request->expects($this->at(0))
            ->method('get')
            ->with('repository')
            ->willReturn($repository);
        $this->request->expects($this->at(1))
            ->method('get')
            ->with('_route')
            ->willReturn('route_xyz');
        $this->request->expects($this->once())
            ->method('getSchemeAndHttpHost')
            ->willReturn($actualPage);

        // adding referer
        $this->request->headers = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->headers->expects($this->once())
            ->method('get')
            ->with('referer')
            ->willReturn($url);

        $this->controllerEvent->expects($this->once())->method('getRequest')
            ->willReturn($this->request);
        $this->controllerEvent->expects($this->once())->method('getController')
            ->willReturn([$controller, $method]);

        $this->persister
            ->expects($this->never())
            ->method('incrementTotalAccess');

        $this->persister
            ->expects($this->never())
            ->method('incrementRepositoryAccess')
            ->with($repository)
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('addRepositoryToLatestAccessed')
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('incrementRepositoryAccessType')
            ->with($repository, $method)
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('addReferer')
            ->with($url)
            ->willReturnSelf();

        $this->listener->onKernelController($this->controllerEvent);
    }

    public function testWhenRepositoryIsNullShouldNotIncrement(): void
    {
        $controller = new StdClass();
        $method = 'boomAction';
        $repository = null;
        $url = 'https://poser.pugx.org';
        $this->request->expects($this->at(0))
            ->method('get')
            ->with('repository')
            ->willReturn($repository);

        // adding referer
        $this->request->headers = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->headers->expects($this->never())
            ->method('get')
            ->with('referer');

        $this->controllerEvent->expects($this->once())->method('getRequest')
            ->willReturn($this->request);
        $this->controllerEvent->expects($this->once())->method('getController')
            ->willReturn([$controller, $method]);

        $this->persister
            ->expects($this->never())
            ->method('incrementTotalAccess');

        $this->persister
            ->expects($this->never())
            ->method('incrementRepositoryAccess')
            ->with($repository)
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('addRepositoryToLatestAccessed')
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('incrementRepositoryAccessType')
            ->with($repository, $method)
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('addReferer')
            ->with($url)
            ->willReturnSelf();

        $this->listener->onKernelController($this->controllerEvent);
    }

    public function testWhenIsRoutedFromHomelShouldNotIncrement(): void
    {
        $controller = new StdClass();
        $method = 'boomAction';
        $repository = 'repository';
        $url = 'https://poser.pugx.org';
        $this->request->expects($this->at(0))
            ->method('get')
            ->with('repository')
            ->willReturn($repository);

        $this->request->expects($this->at(1))
            ->method('get')
            ->with('_route')
            ->willReturn('home');

        // adding referer
        $this->request->headers = $this->getMockBuilder(ParameterBag::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->headers->expects($this->never())
            ->method('get')
            ->with('referer');

        $this->controllerEvent->expects($this->once())->method('getRequest')
            ->willReturn($this->request);
        $this->controllerEvent->expects($this->once())->method('getController')
            ->willReturn([$controller, $method]);

        $this->persister
            ->expects($this->never())
            ->method('incrementTotalAccess');

        $this->persister
            ->expects($this->never())
            ->method('incrementRepositoryAccess')
            ->with($repository)
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('addRepositoryToLatestAccessed')
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('incrementRepositoryAccessType')
            ->with($repository, $method)
            ->willReturnSelf();

        $this->persister
            ->expects($this->never())
            ->method('addReferer')
            ->with($url)
            ->willReturnSelf();

        $this->listener->onKernelController($this->controllerEvent);
    }
}
