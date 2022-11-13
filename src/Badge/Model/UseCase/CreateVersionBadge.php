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

/**
 * Create the 'version' image using a generator `Poser`.
 */
final class CreateVersionBadge extends BaseCreatePackagistImage
{
    private const COLOR_STABLE = '28a3df';
    private const COLOR_UNSTABLE = 'e68718';
    private const SUBJECT_STABLE = 'stable';
    private const SUBJECT_UNSTABLE = 'unstable';
    private const TEXT_NO_STABLE_RELEASE = 'No Release';

    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    /**
     * @throws \InvalidArgumentException
     */
    public function createStableBadge(string $repository, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): CacheableBadge
    {
        return $this->createBadgeFromRepository(
            $repository,
            self::SUBJECT_STABLE,
            self::COLOR_STABLE,
            $format,
            $style,
            'stable',
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function createUnstableBadge(string $repository, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): CacheableBadge
    {
        return $this->createBadgeFromRepository(
            $repository,
            self::SUBJECT_UNSTABLE,
            self::COLOR_UNSTABLE,
            $format,
            $style,
            'unstable',
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    protected function prepareText(Package $package, ?string $context): string
    {
        if ('stable' === $context) {
            $latestStableVersion = $package->getLatestStableVersion();

            return $latestStableVersion ?: self::TEXT_NO_STABLE_RELEASE;
        }

        if (null !== $latestUnstableVersion = $package->getLatestUnstableVersion()) {
            return $latestUnstableVersion;
        }

        return self::TEXT_NO_STABLE_RELEASE;
    }
}
