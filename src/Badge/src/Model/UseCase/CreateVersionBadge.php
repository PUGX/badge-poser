<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PUGX\Badge\Model\UseCase;

/**
 * Create the 'license' image using a generator `Poser`
 */
class CreateVersionBadge extends BaseCreatePackagistImage
{
    CONST COLOR_STABLE = '28a3df';
    CONST COLOR_UNSTABLE = 'e68718';
    CONST SUBJECT_STABLE = 'stable';
    CONST SUBJECT_UNSTABLE = 'unstable';
    CONST TEXT_NO_STABLE_RELEASE = 'No Release';

    /**
     * @param string $repository
     * @param string $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createStableBadge($repository, $format = 'svg')
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT_STABLE, self::COLOR_STABLE, $format, 'stable');
    }

    /**
     * @param $repository
     * @param string $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createUnstableBadge($repository, $format = 'svg')
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT_UNSTABLE, self::COLOR_UNSTABLE, $format, 'unstable');
    }

    protected function prepareText($package, $context = null)
    {
        if ('stable' == $context && $package->hasStableVersion()) {
            return $package->getLatestStableVersion();
        } elseif ('stable' == $context) {
            return self::TEXT_NO_STABLE_RELEASE;
        } elseif ($package->hasUnstableVersion()) {
            return $package->getLatestUnstableVersion();
        }

        return self::TEXT_NO_STABLE_RELEASE;
    }
}
