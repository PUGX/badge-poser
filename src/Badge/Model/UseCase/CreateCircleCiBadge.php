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

use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Service\CircleCiClientInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use UnexpectedValueException;

/**
 * Class CreateCircleCiBadge
 * Create the 'CircleCi' image using a generator `Poser`.
 */
class CreateCircleCiBadge extends BaseCreatePackagistImage
{
    private const COLOR_PASSING = '42BD1B';
    private const COLOR_FAILING = 'D75B48';
    private const TEXT_PASSING = 'passing';
    private const TEXT_FAILING = 'failing';
    private const SUBJECT = 'build';

    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    protected string $text = self::SUBJECT;

    protected CircleCiClientInterface $circleCiClient;

    public function __construct(PackageRepositoryInterface $packageRepository, CircleCiClientInterface $circleCiClient)
    {
        parent::__construct($packageRepository);
        $this->circleCiClient = $circleCiClient;
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function createCircleCiBadge(string $repository, string $branch = 'master', string $format = 'svg'): CacheableBadge
    {
        try {
            //check if the repo exist
            \str_replace('.git', '', $this->packageRepository
                ->fetchByRepository($repository)
                ->getRepository()
            );

            $response = $this->circleCiClient->getBuilds($repository, $branch);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                return $this->createDefaultBadge($format);
            }

            $builds = \json_decode($response->getContent(), true);
        } catch (Throwable $e) {
            return $this->createDefaultBadge($format);
        }

        if (\count($builds) < 1) {
            return $this->createDefaultBadge($format);
        }

        $build = \current($builds);

        if ('success' === $build['status']) {
            $color = self::COLOR_PASSING;
            $this->text = self::TEXT_PASSING;
        } else {
            $color = self::COLOR_FAILING;
            $this->text = self::TEXT_FAILING;
        }

        return $this->createBadgeFromRepository(
            $repository,
            self::SUBJECT,
            $color,
            $format,
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
