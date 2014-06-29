<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Package;

use Packagist\Api\Client;
use Packagist\Api\Result\Package as ApiPackage;

use \UnexpectedValueException;

/**
 * This class is intended to load ApiPackage, create, and work with the Package object
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class PackageService
{
    private static $packageClass;
    private $client;
    private $normalizer;

    public function __construct(Client $packagistClient, $packageClass = '\PUGX\Badge\Package\Package', $textNormalizer)
    {
        self::$packageClass = $packageClass;
        $this->client = $packagistClient;
        $this->normalizer = $textNormalizer;
    }

    /**
     * Returns package if founded.
     *
     * @param string $repository
     *
     * @return Package
     *
     * @throws UnexpectedValueException
     */
    public function fetchPackage($repository)
    {
        $apiPackage = $this->client->get($repository);
        if ($apiPackage && $apiPackage instanceof ApiPackage) {
            // create a new Package from the ApiPackage
            return Package::createFromApi($apiPackage);
        }

        throw new UnexpectedValueException(sprintf('Impossible to found repository "%s"', $repository));
    }

    /**
     * Take the Type of the Downloads (total, monthly or daily).
     *
     * @param Package $package
     * @param string  $type
     *
     * @return string
     */
    public function getPackageDownloads(Package $package, $type)
    {
        $statsType = 'get' . ucfirst($type);

        if ($package && ($download = $package->getDownloads()) && $download instanceof \Packagist\Api\Result\Package\Downloads) {
            return $this->normalizer->normalize($download->{$statsType}());
        }
    }
}
