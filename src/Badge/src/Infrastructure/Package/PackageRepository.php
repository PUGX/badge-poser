<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Infrastructure\Package;

use Packagist\Api\Client;
use Packagist\Api\Result\Package as ApiPackage;
use PUGX\Badge\Model\PackageRepositoryInterface;

use \UnexpectedValueException;

/**
 * This class is intended to load ApiPackage, create, and work with the Package object
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class PackageRepository implements PackageRepositoryInterface
{
    private static $packageClass;
    private $client;

    public function __construct(Client $packagistClient, $packageClass = '\PUGX\Badge\Model\Package')
    {
        self::$packageClass = $packageClass;
        $this->client = $packagistClient;
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
    public function fetchByRepository($repository)
    {
        $apiPackage = $this->client->get($repository);
        if ($apiPackage && $apiPackage instanceof ApiPackage) {
            // create a new Package from the ApiPackage
            $class = self::$packageClass;

            return $class::createFromApi($apiPackage);
        }

        throw new UnexpectedValueException(sprintf('Impossible to fetch package by "%s" repository.', $repository));
    }
}
