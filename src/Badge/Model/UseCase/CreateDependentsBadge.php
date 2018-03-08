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

/**
 * Create the 'dependents' image with the standard Font and standard Image.
 */
class CreateDependentsBadge extends BaseCreatePackagistImage
{
    const COLOR = '007ec6';
    const SUBJECT = 'dependents';

    /**
     * @param $repository
     * @param $format
     *
     * @return Badge
     */
    public function createDependentsBadge(string $repository, string $format = 'svg') : Badge
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format);
    }

    /**
     * @param Package $package
     * @param null|string $context
     * @return string
     */
    protected function prepareText(Package $package, $context = null)
    {
        return "{$package->getDependents()}";
    }
}
