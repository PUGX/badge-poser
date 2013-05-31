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

use PUGX\BadgeBundle\Exception\BadFunctionCallException;
use Packagist\Api\Result\Package as ApiPackage;

class Package implements PackageInterface, ApiPackageInterface
{
    private $originalObject;
    private $latestStableVersion = null;
    private $latestUnstableVersion = null;

    public function setLatestStableVersion($version)
    {
        $this->latestStableVersion = $version;
    }

    public function getLatestStableVersion()
    {
        return $this->latestStableVersion;
    }

    public function hasStableVersion() {
        return isset($this->latestStableVersion);
    }

    public function setLatestUnstableVersion($version)
    {
        $this->latestUnstableVersion = $version;
    }

    public function getLatestUnstableVersion()
    {
        return $this->latestUnstableVersion;
    }

    public function hasUnstableVersion() {
        return isset($this->latestUnstableVersion);
    }

    public function setOriginalObject(ApiPackage $originalObject)
    {
        $this->originalObject = $originalObject;
    }

    public function getOriginalObject()
    {
        return $this->originalObject;
    }

    // original object's property

    public function getName()
    {
        return $this->getOriginalObject()->getName();
    }

    public function getDescription()
    {
        return $this->getOriginalObject()->getDescription();
    }

    public function getDownloads()
    {
        return $this->getOriginalObject()->getDownloads();
    }

    public function getFavers()
    {
        return $this->getOriginalObject()->getFavers();
    }

    public function getMaintainers()
    {
        return $this->getOriginalObject()->getMaintainers();
    }

    public function getTime()
    {
        return $this->getOriginalObject()->getTime();
    }

    public function getRepository()
    {
        return $this->getOriginalObject()->getRepository();
    }

    public function getType()
    {
        return $this->getOriginalObject()->getType();
    }

    public function getVersions()
    {
        return $this->getOriginalObject()->getVersions();
    }
}