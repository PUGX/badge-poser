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

use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Service\ClientStrategy;
use App\Badge\ValueObject\Repository;
use Packagist\Api\Client as PackagistClient;
use Packagist\Api\Result\Package as ApiPackage;
use UnexpectedValueException;

/**
 * This class is intended to load ApiPackage, create, and work with the Package object.
 */
final class PackageRepository implements PackageRepositoryInterface
{
    private static string $packageClass;

    public function __construct(
        private PackagistClient $packagistClient,
        private ClientStrategy $clientStrategy,
        string $packageClass = Package::class
    ) {
        self::$packageClass = $packageClass;
    }

    /**
     * Returns package if founded.
     *
     * @throws UnexpectedValueException
     */
    public function fetchByRepository(string $repository, bool $withDefaultBranch = false): Package
    {
        $apiPackage = $this->packagistClient->get($repository);
        if (!$apiPackage instanceof ApiPackage) {
            throw new UnexpectedValueException(\sprintf('Impossible to fetch package by "%s" repository.', $repository));
        }

        $repositoryInfo = Repository::createFromRepositoryUrl($apiPackage->getRepository());

        /** @var Package $class */
        $class = self::$packageClass;

        $package = $class::createFromApi($apiPackage);
        if (true === $withDefaultBranch) {
            $defaultBranch = $this->clientStrategy->getDefaultBranch($repositoryInfo);
            $package = $package->withDefaultBranch($defaultBranch);
        }

        return $package;
    }
}
