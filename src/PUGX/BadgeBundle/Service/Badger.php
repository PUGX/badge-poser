<?php

/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bridge\Monolog\Logger;
use Packagist\Api\Client;
use PUGX\BadgeBundle\Event\PackageEvent;
use PUGX\BadgeBundle\Exception\UnexpectedValueException;
use Packagist\Api\Result\Package\Version;

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
     * @param string $repositoryName
     *
     * @return \Packagist\Api\Result\Package\Downloads
     * @throws \PUGX\BadgeBundle\Exception\UnexpectedValueException
     */
    private function doGetPackageDownloads($repositoryName)
    {
        $package = $this->getPackage($repositoryName);
        if ($package && ($download = $package->getDownloads()) && $download instanceof \Packagist\Api\Result\Package\Downloads) {
            return $download;
        }

        throw new UnexpectedValueException(sprintf('Impossible to found repository "%s"', $repositoryName));

    }

    /**
     * Returns package if found.
     *
     * @param string $repository
     *
     * @return \Packagist\Api\Result\Package|null
     */
    protected function getPackage($repository)
    {
        try {
            $package = $this->client->get($repository);
            if ($package && $package instanceof \Packagist\Api\Result\Package) {
                return $package;
            }
        } catch (\Exception $e){

        }

        return null;
    }

    //to be refactored

    /**
     * This function try to understand if the repository is stable or not.
     *
     * @param Version $version
     *
     * @return Boolean
     */
    protected function filterStableVersions(Version $version)
    {
        $notStableKeys = array('develop', 'master', 'dev', 'RC', 'BETA', 'ALPHA');
        foreach ($notStableKeys as $notStableKey) {
            if (stripos($version->getVersion(), $notStableKey) != false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the latest stable version.
     *
     * @param string $repository
     *
     * @return string|null
     */
    public function getLatestStableVersion($repository)
    {
        $last = null;

        $package = $this->getPackage($repository);
        if ($package && $versions = $package->getVersions()) {
            $stableVersions = array_filter($versions, array($this, 'filterStableVersions'));
            array_walk($stableVersions, function($version) use(&$last){
                if ($version->getVersion() > $last) {
                    $last = $version->getVersion();
                }
            });
        }

        return $last;
    }
    /**
     * @param Version $version
     * @return bool
     */
    protected function filterUnstableVersions(Version $version)
    {
        $notStableKeys = array('develop', 'master', 'dev', 'RC', 'BETA', 'ALPHA');

        foreach ($notStableKeys as $notStableKey) {
            if (stripos($version->getVersion(), $notStableKey) == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $repository
     * @return null
     */
    public function getLatestUnstableVersion($repository)
    {
        $last = null;

        $package = $this->getPackage($repository);
        if ($package && $versions = $package->getVersions()) {
            $unstableVersions = array_filter($versions, array($this, 'filterUnstableVersions'));

            array_walk($unstableVersions, function($version) use(&$last){
                if ($version->getVersion() > $last) {
                    $last = $version->getVersion();
                }
            });
        }

        return $last;
    }

    //end to be refactored
}