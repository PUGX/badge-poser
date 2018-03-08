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
 * Create the 'suggesters' image with the standard Font and standard Image.
 */
class CreateSuggestersBadge extends BaseCreatePackagistImage
{
    CONST COLOR = '007ec6';
    CONST SUBJECT = 'suggesters';

    /**
     * @param $repository
     * @param $format
     *
     * @return Badge
     */
    public function createSuggestersBadge(string $repository, string $format = 'svg') : Badge
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
        return "{$package->getSuggesters()}";
    }
}
