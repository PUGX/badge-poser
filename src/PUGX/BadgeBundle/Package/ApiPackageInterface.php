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

interface ApiPackageInterface
{
    public function getName();

    public function getDescription();

    public function getDownloads();

    public function getFavers();

    public function getMaintainers();

    public function getTime();

    public function getRepository();

    public function getType();

    public function getVersions();

}