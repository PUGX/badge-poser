<?php
/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Package;

use Packagist\Api\Result\Package as ApiPackage;

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