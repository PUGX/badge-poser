<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Service\ClientStrategy;
use App\Badge\ValueObject\Repository;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Create the 'gitattributes' image using a generator `Poser`.
 */
final class CreateGitAttributesBadge extends BaseCreatePackagistImage
{
    private const COLOR_COMMITTED = '96d490';
    private const COLOR_UNCOMMITTED = 'ad6c4b';
    private const COLOR_ERROR = 'aa0000';
    private const GITATTRIBUTES_COMMITTED = 'committed';
    private const GITATTRIBUTES_UNCOMMITTED = 'uncommitted';
    private const GITATTRIBUTES_ERROR = 'checking';
    private const SUBJECT = '.gitattributes';
    private const SUBJECT_ERROR = 'Error';
    private const TIMEOUT_SECONDS = 8;
    private const CONNECT_TIMEOUT_SECONDS = 5;

    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    protected string $text = self::GITATTRIBUTES_ERROR;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        protected ClientInterface $client,
        private ClientStrategy $clientStrategy
    ) {
        parent::__construct($packageRepository);
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws GuzzleException
     */
    public function createGitAttributesBadge(string $repository, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): CacheableBadge
    {
        try {
            $package = $this->fetchPackageWithRepo($repository);
            $repo = \str_replace('.git', '', $package->getRepository());
        } catch (\Exception) {
            return $this->createDefaultBadge($format, $style);
        }

        $repositoryInfo = Repository::createFromRepositoryUrl($repo);

        $response = $this->client->request(
            'HEAD',
            $this->clientStrategy->getRepositoryPrefix($repositoryInfo, $repo).'/'.
            $package->getDefaultBranch().
            '/.gitattributes',
            [
                RequestOptions::TIMEOUT => self::TIMEOUT_SECONDS,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT_SECONDS,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );

        $status = 500;
        if (null !== $response) {
            $status = $response->getStatusCode();
        }

        $this->text = self::GITATTRIBUTES_ERROR;
        $color = self::COLOR_ERROR;
        $subject = self::SUBJECT_ERROR;
        if (200 === $status) {
            $this->text = self::GITATTRIBUTES_COMMITTED;
            $color = self::COLOR_COMMITTED;
            $subject = self::SUBJECT;
        } elseif (404 === $status) {
            $this->text = self::GITATTRIBUTES_UNCOMMITTED;
            $color = self::COLOR_UNCOMMITTED;
            $subject = self::SUBJECT;
        }

        return $this->createBadgeFromRepository(
            $repository,
            $subject,
            $color,
            $format,
            $style,
            null,
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    protected function prepareText(Package $package, ?string $context): string
    {
        return $this->text;
    }
}
