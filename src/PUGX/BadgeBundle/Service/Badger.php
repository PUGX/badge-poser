<?php

namespace PUGX\BadgeBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Monolog\Logger;
use Packagist\Api\Client;
use PUGX\BadgeBundle\Event\PackageEvent;
use PUGX\BadgeBundle\Exception\UnexpectedValueException;

class Badger
{
    private $client;
    private $logger;
    protected $dispatcher;

    public function __construct(Client $packagistClient, EventDispatcherInterface $dispatcher, Logger $logger)
    {
        $this->client = $packagistClient;
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * Just get the download number for that repository.
     *
     * @param string $repositoryName  the 'vendor/reponame'
     * @param string $type            the type of the stats total,monthly or daily
     *
     * @return int
     */
    public function getPackageDownloads($repositoryName, $type)
    {
        $statsType = 'get' . ucfirst($type);

        $this->logger->info(sprintf('download - %s', $repositoryName));
        $this->dispatcher->dispatch('get.package', new PackageEvent($repositoryName, PackageEvent::ACTION_DOWNLOAD));

        $download = $this->doGetPackageDownloads($repositoryName);
        $downloadsTypeNumber = $download->{$statsType}();
        $this->logger->info(sprintf('download - %s - %d', $repositoryName, $downloadsTypeNumber));

        return $downloadsTypeNumber;
    }

    /**
     * Do the get number for that repository.
     *
     * @param $repositoryName
     *
     * @return \Packagist\Api\Result\Package\Downloads
     * @throws \PUGX\BadgeBundle\Exception
     */
    private function doGetPackageDownloads($repositoryName)
    {
        $package = $this->client->get($repositoryName);
        if ($package && ($download = $package->getDownloads()) && $download instanceof \Packagist\Api\Result\Package\Downloads) {

            return $download;
        }

        throw new UnexpectedValueException(sprintf('Impossible to found repository "%s"', $repositoryName));

    }

}