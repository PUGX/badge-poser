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
 * This class is intended to load ApiPackage, create, and work with the Package object
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface PackageRepositoryInterface
{
    /**
     * Returns package if founded.
     *
     * @param string $repository
     *
     * @return Package
     *
     * @throws UnexpectedValueException
     */
    public function fetchByRepository($repository);
}
