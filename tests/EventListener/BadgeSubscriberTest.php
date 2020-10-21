<?php

namespace App\Tests\EventListener;

use App\Badge\Model\Badge;
use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Image;
use App\Badge\Model\UseCase\CreateErrorBadge;
use App\Badge\Service\ImageFactory;
use App\EventListener\BadgeSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BadgeSubscriberTest extends TestCase
{
    /**
     * @var Request&MockObject
     */
    private $request;

    /**
     * @var ExceptionEvent&MockObject
     */
    private $event;

    /**
     * @var Badge&MockObject
     */
    private $errorBadge;

    /**
     * @var CreateErrorBadge&MockObject
     */
    private $useCase;

    /**
     * @var ImageFactory&MockObject
     */
    private $imgFactory;

    /**
     * @var Image&MockObject
     */
    private $img;

    /**
     * @var BadgeSubscriber
     */
    private $badgeSubscriber;

    public function setUp(): void
    {
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exception = new \Exception('An exception msg');
        $this->event = $this->getMockBuilder(ExceptionEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->event->method('getRequest')
            ->willReturn($this->request);
        $this->event->method('getThrowable')
            ->willReturn($exception);

        $this->errorBadge = $this->getMockBuilder(CacheableBadge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $cacheableErrorBadge = $this->getMockBuilder(CacheableBadge::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheableErrorBadge->method('getBadge')
            ->willReturn($this->errorBadge);

        $this->useCase = $this->getMockBuilder(CreateErrorBadge::class)
            ->getMock();
        $this->useCase->method('createErrorBadge')
            ->with($exception, 'svg')
            ->willReturn($cacheableErrorBadge);

        $this->img = $this->getMockBuilder(Image::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->imgFactory = $this->getMockBuilder(ImageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->imgFactory->method('createFromBadge')
            ->with($this->errorBadge)
            ->willReturn($this->img);

        $this->badgeSubscriber = new BadgeSubscriber($this->useCase, $this->imgFactory);
    }

    public function testItIsSubscribedToKernelExceptionEvent(): void
    {
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, $this->badgeSubscriber->getSubscribedEvents());
    }

    public function testDontHandleErrorsForNotBadgeControllers(): void
    {
        $this->request->method('get')
            ->with('_controller')
            ->willReturn('notABadgeController');

        $this->event->expects($this->never())
            ->method('setResponse');

        $this->badgeSubscriber->onKernelException($this->event);
    }

    public function testHandleErrorsForBadgeControllers(): void
    {
        $this->request->method('get')
            ->with('_controller')
            ->willReturn('App\Controller\Badge\ABadgeController');

        $this->event->expects($this->once())
            ->method('setResponse')
            ->with($this->callback(function (Response $response) {
                if ($response->getContent() != (string) $this->img) {
                    return false;
                }

                return Response::HTTP_INTERNAL_SERVER_ERROR === $response->getStatusCode();
            }));

        $this->badgeSubscriber->onKernelException($this->event);
    }
}
