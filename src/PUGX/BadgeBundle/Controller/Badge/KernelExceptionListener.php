<?php

namespace PUGX\BadgeBundle\Controller\Badge;

use PUGX\Badge\Model\UseCase\CreateErrorBadge;
use PUGX\Badge\Service\ImageFactory;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use PUGX\Badge\Infrastructure\ResponseFactory;

class KernelExceptionListener
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

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->isABadgeController($event->getRequest()->get('_controller'))) {
            return;
        }

        $badge = $this->useCase->createErrorBadge($event->getException(), 'svg');
        $image = $this->imageFactory->createFromBadge($badge);

        $response = ResponseFactory::createFromImage($image, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }

    private function isABadgeController($controllerName)
    {
        return strpos($controllerName, 'PUGX\BadgeBundle\Controller\Badge') === 0;
    }
}
