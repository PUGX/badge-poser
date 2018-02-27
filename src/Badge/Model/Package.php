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

use Packagist\Api\Result\Package as ApiPackage;

/**
 * Class Package, decorates the Packagist Package.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class Package
{
    private static $modifierRegex = '[._-]?(?:(stable|beta|b|RC|alpha|a|patch|pl|p)(?:[.-]?(\d+))?)?([.-]?dev)?';
    private $license;
    private $originalObject;
    private $latestStableVersion = null;
    private $latestUnstableVersion = null;
    private $latestStableVersionNormalized = null;
    private $latestUnstableVersionNormalized = null;

    private function __construct(ApiPackage $apiPackage)
    {
       $this->setOriginalObject($apiPackage);
       $this->calculateLatestVersions();
    }

    /**
     * Create a new Package decorated with the Api Package.
     *
     * @param ApiPackage $apiPackage
     *
     * @return Package
     */
    public static function createFromApi(ApiPackage $apiPackage)
    {
        $package = new self($apiPackage);

        return $package;
    }

    /**
     * Take the Type of the Downloads (total, monthly or daily).
     *
     * @param string $type
     *
     * @return string
     */
    public function getPackageDownloads($type)
    {
        $statsType = 'get' . ucfirst($type);
        if (($download = $this->getDownloads()) && $download instanceof \Packagist\Api\Result\Package\Downloads) {
            return $download->{$statsType}();
        }
    }

    /**
     * Set the latest Stable and the latest Unstable version from a Package.
     *
     * @return Package
     */
    private function calculateLatestVersions()
    {
        $versions = $this->getVersions();

        foreach ($versions as $name => $version) {

            $currentVersionName = $version->getVersion();
            $versionNormalized = $version->getVersionNormalized();

            $aliases = $this->getBranchAliases($version);
            if (null !== $aliases && array_key_exists($currentVersionName, $aliases)) {
                $currentVersionName = $aliases[$currentVersionName];
            }

            $functionName = 'Unstable';
            if ('stable' == self::parseStability($currentVersionName)) {
                $functionName = 'Stable';
            }

            if (version_compare($versionNormalized, $this->{'getLatest' . $functionName . 'VersionNormalized'}()) > 0) {
                $this->{'setLatest' . $functionName . 'Version'}($currentVersionName);
                $this->{'setLatest' . $functionName . 'VersionNormalized'}($versionNormalized);

                $license = $version->getLicense();
                if (is_array($license) && count($license)>0) {
                    $license = implode(',',$license);
                }
                $this->setLicense($license);
            }
        }

        return $this;
    }

    /**
     * Get all the branch aliases.
     *
     * @param ApiPackage\Version $version
     *
     * @return null|array
     */
    private function getBranchAliases(ApiPackage\Version $version)
    {
        $extra = $version->getExtra();
        if (null !== $extra
            && isset($extra["branch-alias"])
            && is_array($extra["branch-alias"])
        ) {
            return $extra["branch-alias"];
        }

        return null;
    }

    /**
     * Returns the stability of a version.
     *
     * This function is part of Composer.
     *
     * (c) Nils Adermann <naderman@naderman.de>
     * Jordi Boggiano <j.boggiano@seld.be>
     *
     * @param string $version
     *
     * @return string
     */
    public static function parseStability($version)
    {
        $version = preg_replace('{#.+$}i', '', $version);

        if ('dev-' === substr($version, 0, 4) || '-dev' === substr($version, -4)) {
            return 'dev';
        }

        preg_match('{' . self::$modifierRegex . '$}i', strtolower($version), $match);
        if (!empty($match[3])) {
            return 'dev';
        }

        if (!empty($match[1])) {
            if ('beta' === $match[1] || 'b' === $match[1]) {
                return 'beta';
            }
            if ('alpha' === $match[1] || 'a' === $match[1]) {
                return 'alpha';
            }
            if ('rc' === $match[1]) {
                return 'RC';
            }
        }

        return 'stable';
    }

    public function getLicense()
    {
        return $this->license;
    }

    public function getLatestStableVersion()
    {
        return $this->latestStableVersion;
    }

    public function hasStableVersion()
    {
        return isset($this->latestStableVersion);
    }

    public function getLatestUnstableVersion()
    {
        return $this->latestUnstableVersion;
    }

    /**
     * @return null
     */
    public function getLatestStableVersionNormalized()
    {
        return $this->latestStableVersionNormalized;
    }

    public function getLatestUnstableVersionNormalized()
    {
        return $this->latestUnstableVersionNormalized;
    }

    public function hasUnstableVersion()
    {
        return isset($this->latestUnstableVersion);
    }

    public function getOriginalObject()
    {
        return $this->originalObject;
    }

    private function setLatestUnstableVersionNormalized($latestUnstableVersionNormalized)
    {
        $this->latestUnstableVersionNormalized = $latestUnstableVersionNormalized;
    }

    private function setLicense($license)
    {
        $this->license = $license;
    }

    private function setLatestStableVersion($version)
    {
        $this->latestStableVersion = $version;
    }

    private function setLatestUnstableVersion($version)
    {
        $this->latestUnstableVersion = $version;
    }

    private function setLatestStableVersionNormalized($latestStableVersionNormalized)
    {
        $this->latestStableVersionNormalized = $latestStableVersionNormalized;
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

    private function setOriginalObject(ApiPackage $originalObject)
    {
        $this->originalObject = $originalObject;
    }
}
