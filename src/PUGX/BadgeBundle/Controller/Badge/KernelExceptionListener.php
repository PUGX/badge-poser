<?php

namespace PUGX\BadgeBundle\Controller\Badge;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use PUGX\Badge\Infrastructure\ResponseFactory;

class KernelExceptionListener
{
    /** @var CreateErrorBadge */
    private $useCase;
    private $imageFactory;

    public function __construct($useCase, $imageFactory)
    {
        $this->useCase = $useCase;
        $this->imageFactory = $imageFactory;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->isABadgeController($event->getRequest()->get('_controller'))) {
            return;
        }

        $request = $event->getRequest()->getRequestFormat();

        $badge = $this->useCase->createErrorBadge($event->getException(), 'svg');
        $image = $this->imageFactory->createFromBadge($badge);

        $response = ResponseFactory::createFromImage($image, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }

    private function isABadgeController($controllerName)
    {
        if (strpos($controllerName, 'PUGX\BadgeBundle\Controller\Badge') === 0) {
            return true;
        }

        return false;
    }
}
