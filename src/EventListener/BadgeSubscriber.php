<?php

namespace App\EventListener;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateErrorBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class BadgeSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CreateErrorBadge $useCase, private readonly ImageFactory $imageFactory)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isABadgeController($event->getRequest()->get('_controller'))) {
            return;
        }

        $cacheableBadge = $this->useCase->createErrorBadge($event->getThrowable(), 'svg');
        $image = $this->imageFactory->createFromBadge($cacheableBadge);

        $response = ResponseFactory::createFromImage($image, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }

    private function isABadgeController(?string $controllerName): bool
    {
        return null !== $controllerName && \str_starts_with($controllerName, 'App\Controller\Badge');
    }
}
