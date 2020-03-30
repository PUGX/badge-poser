<?php

namespace App\EventListener;

use App\Stats\Persister\PersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class StatsSubscriber
 * This class is intended to collect and store usage statistic on Redis.
 */
class StatsSubscriber implements EventSubscriberInterface
{
    private PersisterInterface $client;

    public function __construct(PersisterInterface $client)
    {
        $this->client = $client;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        /** @var Request|null $request */
        $request = $event->getRequest();
        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!\is_array($controller) || null === $request) {
            return;
        }

        $this->persistData($request, $controller[1]);
    }

    /**
     * Return true if the the route of the Request is home.
     */
    private function isRoutedFromHome(Request $request): bool
    {
        return false !== strpos($request->get('_route'), 'home');
    }

    /**
     * Persist data.
     *
     * @param string $controller The controller Name
     */
    public function persistData(Request $request, $controller): bool
    {
        if (null === ($repository = $request->get('repository', null)) || $this->isRoutedFromHome($request)) {
            return false;
        }
        $referer = $request->headers->get('referer');

        $this->client->incrementTotalAccess();
        $this->client->incrementRepositoryAccess($repository);
        $this->client->addRepositoryToLatestAccessed($repository);
        $this->client->incrementRepositoryAccessType($repository, $controller);
        if ($referer) {
            $this->client->addReferer($referer);
        }

        return true;
    }
}
