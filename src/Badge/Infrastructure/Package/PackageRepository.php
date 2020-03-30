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
use Github\Api\Repo;
use Github\Client as GitHubClient;
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
    private GitHubClient $githubClient;

    public function __construct(PackagistClient $packagistClient, GitHubClient $githubClient, string $packageClass = Package::class)
    {
        self::$packageClass = $packageClass;
        $this->packagistClient = $packagistClient;
        $this->githubClient = $githubClient;
    }

    /**
     * Returns package if founded.
     *
     * @throws UnexpectedValueException
     */
    public function fetchByRepository(string $repository): Package
    {
        $apiPackage = $this->packagistClient->get($repository);

        preg_match('/(https)(:\/\/|@)([^\/:]+)[\/:]([^\/:]+)\/(.+)$/', $apiPackage->getRepository(), $matches);

        if (isset($matches[4], $matches[5])) {
            $username = $matches[4];
            $repoName = $matches[5];

            /** @var Repo $repoApi */
            $repoApi = $this->githubClient->api('repo');
            $repoGitHubData = $repoApi->show($username, $repoName);
        }

        if ($apiPackage instanceof ApiPackage && !empty($repoGitHubData)) {
            // create a new Package from the ApiPackage
            /** @var Package $class */
            $class = self::$packageClass;

            return $class::createFromApi($apiPackage, $repoGitHubData);
        }

        throw new UnexpectedValueException(sprintf('Impossible to fetch package by "%s" repository.', $repository));
    }
}
