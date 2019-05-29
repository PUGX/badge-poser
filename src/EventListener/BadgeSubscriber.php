<?php

namespace App\EventListener;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateErrorBadge;
use App\Badge\Service\ImageFactory;
use InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class BadgeSubscriber.
 */
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

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @throws InvalidArgumentException
     */
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

    /**
     * @param string $controllerName
     *
     * @return bool
     */
    private function isABadgeController(string $controllerName): bool
    {
        return 0 === strpos($controllerName, 'App\Controller\Badge');
    }
}
