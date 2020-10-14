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
 * Class PackageRepository
 * This class is intended to load ApiPackage, create, and work with the Package object.
 */
class PackageRepository implements PackageRepositoryInterface
{
    private static string $packageClass;
    private PackagistClient $packagistClient;

    private ClientStrategy $clientStrategy;

    public function __construct(
        PackagistClient $packagistClient,
        ClientStrategy $clientStrategy,
        string $packageClass = Package::class
    ) {
        self::$packageClass = $packageClass;
        $this->packagistClient = $packagistClient;
        $this->clientStrategy = $clientStrategy;
    }

    /**
     * Returns package if founded.
     *
     * @throws UnexpectedValueException
     */
    public function fetchByRepository(string $repository): Package
    {
        $apiPackage = $this->packagistClient->get($repository);
        if (!$apiPackage instanceof ApiPackage) {
            throw new UnexpectedValueException(\sprintf('Impossible to fetch package by "%s" repository.', $repository));
        }

        $repositoryInfo = Repository::createFromRepositoryUrl($apiPackage->getRepository());

        $defaultBranch = $this->clientStrategy->getDefaultBranch($repositoryInfo);

        /** @var Package $class */
        $class = self::$packageClass;

        return $class::createFromApi($apiPackage, ['default_branch' => $defaultBranch]);
    }
}
