<?php

namespace App\EventListener;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateErrorBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class BadgeSubscriber implements EventSubscriberInterface
{
    /**
     * @var CreateErrorBadge
     */
    private $useCase;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    public function __construct(CreateErrorBadge $useCase, ImageFactory $imageFactory)
    {
        $this->useCase = $useCase;
        $this->imageFactory = $imageFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION  => 'onKernelException',
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        if (!$this->isABadgeController($event->getRequest()->get('_controller'))) {
            return;
        }

        $badge = $this->useCase->createErrorBadge($event->getException(), 'svg');
        $image = $this->imageFactory->createFromBadge($badge);

        $response = ResponseFactory::createFromImage($image, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }

    private function isABadgeController($controllerName): bool
    {
        return strpos($controllerName, 'App\Controller\Badge') === 0;
    }
}
