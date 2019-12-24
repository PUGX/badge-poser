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
use App\Badge\Model\Package;
use InvalidArgumentException;

/**
 * Class CreateVersionBadge
 * Create the 'version' image using a generator `Poser`.
 */
class CreateVersionBadge extends BaseCreatePackagistImage
{
    private const COLOR_STABLE = '28a3df';
    private const COLOR_UNSTABLE = 'e68718';
    private const SUBJECT_STABLE = 'stable';
    private const SUBJECT_UNSTABLE = 'unstable';
    private const TEXT_NO_STABLE_RELEASE = 'No Release';

    /**
     * @throws InvalidArgumentException
     */
    public function createStableBadge(string $repository, string $format = 'svg'): Badge
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT_STABLE, self::COLOR_STABLE, $format, 'stable');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createUnstableBadge(string $repository, string $format = 'svg'): Badge
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT_UNSTABLE, self::COLOR_UNSTABLE, $format, 'unstable');
    }

    /**
     * @param string|null $context
     *
     * @return mixed|string
     */
    protected function prepareText(Package $package, $context = null)
    {
        if ('stable' === $context) {
            return $package->hasStableVersion() ? $package->getLatestStableVersion() : self::TEXT_NO_STABLE_RELEASE;
        }

        if ($package->hasUnstableVersion()) {
            return $package->getLatestUnstableVersion();
        }

        return self::TEXT_NO_STABLE_RELEASE;
    }
}
