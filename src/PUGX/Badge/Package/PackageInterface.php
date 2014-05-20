<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Package;
/**
 * Class PackageInterface
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
interface PackageInterface
{
    public function setLatestStableVersion($version);

    public function getLatestStableVersion();

    public function hasStableVersion();

    public function setLatestUnstableVersion($version);

    public function getLatestUnstableVersion();

    public function hasUnstableVersion();

    public function getName();

}
