<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model;

use UnexpectedValueException;

/**
 * This interface is intended to load ApiPackage, create, and work with the Package object.
 */
interface PackageRepositoryInterface
{
    /**
     * Returns package if found.
     *
     * @throws UnexpectedValueException
     */
    public function fetchByRepository(string $repository, bool $withDefaultBranch = false): Package;
}
