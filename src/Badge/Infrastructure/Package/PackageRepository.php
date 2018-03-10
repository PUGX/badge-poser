<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Infrastructure\Package;

use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Model\Package;
use Packagist\Api\Client;
use Packagist\Api\Result\Package as ApiPackage;
use \UnexpectedValueException;

/**
 * Class PackageRepository
 * This class is intended to load ApiPackage, create, and work with the Package object
 * @package App\Badge\Infrastructure\Package
 */
class PackageRepository implements PackageRepositoryInterface
{
    private static $packageClass;
    private $client;

    public function __construct(Client $packagistClient, $packageClass = Package::class)
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
    public function fetchByRepository(string $repository): Package
    {
        $apiPackage = $this->client->get($repository);

        if ($apiPackage && $apiPackage instanceof ApiPackage) {
            // create a new Package from the ApiPackage
            /** @var Package $class */
            $class = self::$packageClass;

            return $class::createFromApi($apiPackage);
        }

        throw new UnexpectedValueException(sprintf('Impossible to fetch package by "%s" repository.', $repository));
    }
}
