<?php

namespace App\Tests\EventListener;

use App\Event\BadgeEvent;
use App\EventListener\BadgeLoggerSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class BadgeLoggerSubscriberTest extends TestCase
{
    /** @var MockObject|LoggerInterface */
    private $logger;

    private BadgeLoggerSubscriber $badgeLoggerSubscriber;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->badgeLoggerSubscriber = new BadgeLoggerSubscriber($this->logger, []);
    }

    public function testItIsSubscribedToEvents(): void
    {
        self::assertArrayHasKey(BadgeEvent::class, $this->badgeLoggerSubscriber::getSubscribedEvents());
        self::assertArrayHasKey(ResponseEvent::class, $this->badgeLoggerSubscriber::getSubscribedEvents());
    }

    public function testPopulateBadgeEventData(): void
    {
        $badgeEvent = $this->getMockBuilder(BadgeEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $badgeEvent->expects(self::once())
            ->method('getData');

        $this->badgeLoggerSubscriber->onBadgeEvent($badgeEvent);
    }

    public function testOnKernelResponse(): void
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $request->method('get')
            ->with('_controller')
            ->willReturn('App\Controller\Badge');
        $request->headers = $this->getMockBuilder(HeaderBag::class)
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->createMock(Response::class);
        $response->headers = $this->getMockBuilder(HeaderBag::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseEvent = new ResponseEvent($kernel, $request, 1, $response);

        $request->expects(self::once())->method('getRequestUri');
        $this->logger->expects(self::once())->method('info');

        $this->badgeLoggerSubscriber->onKernelResponse($responseEvent);
    }
}
