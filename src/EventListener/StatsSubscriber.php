<?php

namespace App\EventListener;

use App\Stats\Persister\PersisterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * This class is intended to collect and store usage statistic on Redis.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class StatsSubscriber implements EventSubscriberInterface
{
    private $client;

    public function __construct(PersisterInterface $client)
    {
        $this->client = $client;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER  => 'onKernelController',
        ];
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
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
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isRoutedFromHome(Request $request): bool
    {
        return (strpos($request->get('_route'), 'home') !== false);
    }

    /**
     * Persist data.
     *
     * @param Request $request
     * @param string  $controller The controller Name
     *
     * @return bool
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
