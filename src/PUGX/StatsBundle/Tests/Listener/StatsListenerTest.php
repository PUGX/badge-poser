<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $url = 'https://poser.pugx.org';
        $this->request->expects($this->once())->method('get')
            ->will($this->returnValue($repository));

        // adding referer
        $this->request->headers = $this->getMockBuilder('Symfony\Component\HttpFoundation\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->headers->expects($this->once())
            ->method('get')
            ->with('referer')
            ->will($this->returnValue($url));

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
        $this->persister->expects($this->once())->method('addReferer')
            ->with($url)
            ->will($this->returnSelf());

        $this->listener->onKernelController($this->controllerEvent);
    }

}