<?php

namespace PUGX\BadgeBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Monolog\Logger;
use Packagist\Api\Client;
use PUGX\BadgeBundle\Event\PackageEvent;
use PUGX\BadgeBundle\Exception;

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
     * @param string $repositoryName
     *
     * @return \Packagist\Api\Result\Package\Downloads The Download entity or null
     */
    public function getPackageDownloads($repositoryName)
    {
        $this->logger->info(sprintf('download - %s', $repositoryName));
        $this->dispatcher->dispatch('get.package', new PackageEvent($repositoryName, PackageEvent::ACTION_DOWNLOAD));

        $download = null;
        try {
            $download = $this->doGetPackageDownloads($repositoryName);
            $this->logger->info(sprintf('download - %s - %d', $repositoryName, $download->getTotal()));
        } catch (\Exception $e) {
            // do nothing we want to catch all the exception.
            $this->logger->error(sprintf('error during download of %s', $repositoryName));
        }

        return $download;
    }

    /**
     * Do the get number for that repository.
     *
     * @param string $repositoryName
     *
     * @return \Packagist\Api\Result\Package\Downloads The Download entity
     */
    private function doGetPackageDownloads($repositoryName)
    {
        $package = $this->client->get($repositoryName);
        if ($package && ($download = $package->getDownloads()) && $download instanceof \Packagist\Api\Result\Package\Downloads) {

            return $download;
        }

        throw new Exception(sprintf('Impossibile to found repository "%s"', $repositoryName));

    }

}