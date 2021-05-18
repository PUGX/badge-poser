<?php

namespace App\EventListener;

use App\Event\BadgeEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class BadgeLoggerSubscriber implements EventSubscriberInterface
{
    /**
     * BadgeLoggerSubscriber constructor.
     * https://symfony.com/doc/current/logging/channels_handlers.html#configure-additional-channels-without-tagged-services.
     *
     * @param LoggerInterface $badgesLogger
     */
    public function __construct(private LoggerInterface $badgesLogger, private array $badgeEventData = [])
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => 'onKernelResponse',
            BadgeEvent::class => 'onBadgeEvent',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$this->isABadgeController($event->getRequest()->get('_controller'))) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $this->badgesLogger->info(
            $request->getRequestUri(),
            [
                'badgeData' => $this->badgeEventData,
                'requestHeaders' => $request->headers->all(),
                'responseHeaders' => $response->headers->all(),
            ]
        );
    }

    public function onBadgeEvent(BadgeEvent $badgeEvent): void
    {
        $this->badgeEventData = $badgeEvent->getData();
    }

    private function isABadgeController(?string $controllerName): bool
    {
        return null !== $controllerName && \str_starts_with($controllerName, 'App\Controller\Badge');
    }
}
