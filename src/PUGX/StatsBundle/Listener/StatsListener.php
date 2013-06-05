<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\StatsBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use PUGX\StatsBundle\Service\PersisterInterface;

/**
 * Class StatsListener
 * This class is intended to collect and store usage statistic on Redis.
 *
 * @package PUGX\BadgeBundle\Listener
 */
class StatsListener
{
    private $client;

    public function __construct(PersisterInterface $client)
    {
        $this->client = $client;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        /*
         * $controller passed can be either a class or a Closure. This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller) || null === $request) {
            return;
        }

        $this->persistData($request, $controller[1]);
    }

    /**
     * Persist data.
     *
     * @param Request $request    The request
     * @param string $controller The controller Name
     *
     * @return Boolean
     */
    public function persistData(Request $request, $controller)
    {
        if (null === ($repository = $request->get('repository', null))) {
            return;
        }

        $this->client->incrementTotalAccess();
        $this->client->incrementRepositoryAccess($repository);
        $this->client->addRepositoryToLatestAccessed($repository);
        $this->client->incrementRepositoryAccessType($repository, $controller);

        return true;
    }
}