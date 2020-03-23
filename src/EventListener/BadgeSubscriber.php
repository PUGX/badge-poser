<?php

namespace App\EventListener;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateErrorBadge;
use App\Badge\Service\ImageFactory;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class BadgeSubscriber.
 */
class BadgeSubscriber implements EventSubscriberInterface
{
    private CreateErrorBadge $useCase;

    private ImageFactory $imageFactory;

    public function __construct(CreateErrorBadge $useCase, ImageFactory $imageFactory)
    {
        $this->useCase = $useCase;
        $this->imageFactory = $imageFactory;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @throws InvalidArgumentException
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$this->isABadgeController($event->getRequest()->get('_controller'))) {
            return;
        }

        $badge = $this->useCase->createErrorBadge($event->getThrowable(), 'svg');
        $image = $this->imageFactory->createFromBadge($badge);

        $response = ResponseFactory::createFromImage($image, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }

    private function isABadgeController(string $controllerName): bool
    {
        return 0 === strpos($controllerName, 'App\Controller\Badge');
    }
}
