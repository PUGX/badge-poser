<?php

namespace App\Tests\EventListener;

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
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class BadgeSubscriberTest extends TestCase
{
    /** @var Request&MockObject */
    private $request;

    /** @var Image|MockObject */
    private $img;

    private BadgeSubscriber $badgeSubscriber;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);

        $errorBadge = $this->createMock(CacheableBadge::class);

        $useCase = $this->createMock(CreateErrorBadge::class);
        $useCase->method('createErrorBadge')
            ->with(new \Exception('An exception msg'), 'svg')
            ->willReturn($errorBadge);

        $this->img = $this->createMock(Image::class);

        $imgFactory = $this->createMock(ImageFactory::class);
        $imgFactory->method('createFromBadge')
            ->with($errorBadge)
            ->willReturn($this->img);

        $this->badgeSubscriber = new BadgeSubscriber($useCase, $imgFactory);
    }

    public function testItIsSubscribedToKernelExceptionEvent(): void
    {
        self::assertArrayHasKey(KernelEvents::EXCEPTION, $this->badgeSubscriber::getSubscribedEvents());
    }

    public function testDontHandleErrorsForNotBadgeControllers(): void
    {
        $this->request->method('get')
            ->with('_controller')
            ->willReturn('notABadgeController');
        $exception = new \Exception('An exception msg');
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, $this->request, 1, $exception);

        $this->badgeSubscriber->onKernelException($event);
        self::assertEmpty($event->getResponse());
    }

    public function testHandleErrorsForBadgeControllers(): void
    {
        $exception = new \Exception('An exception msg');
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, $this->request, 1, $exception);
        $event->setResponse(new Response($this->img, Response::HTTP_INTERNAL_SERVER_ERROR));

        $this->request->method('get')
            ->with('_controller')
            ->willReturn('App\Controller\Badge\ABadgeController');

        $this->badgeSubscriber->onKernelException($event);
        self::assertEquals(500, $event->getResponse()->getStatusCode());
    }
}
