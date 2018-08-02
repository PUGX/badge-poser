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
 * Class Package
 * Decorates the Packagist Package.
 */
class Package
{
    /** @var string */
    private static $modifierRegex = '[._-]?(?:(stable|beta|b|RC|alpha|a|patch|pl|p)(?:[.-]?(\d+))?)?([.-]?dev)?';
    /** @var string */
    private $license;
    /** @var ApiPackage */
    private $originalObject;
    /** @var string */
    private $latestStableVersion;
    /** @var string */
    private $latestUnstableVersion;
    /** @var string */
    private $latestStableVersionNormalized;
    /** @var string */
    private $latestUnstableVersionNormalized;
    /** @var string */
    private $defaultBranch;

    private function __construct(ApiPackage $apiPackage, array $repoGitHubData)
    {
        $this->setOriginalObject($apiPackage);
        $this->calculateLatestVersions();
        $this->defaultBranch = $repoGitHubData['default_branch'];
    }

    /**
     * Create a new Package decorated with the Api Package.
     *
     * @param ApiPackage $apiPackage
     * @param array      $repoGitHubData
     *
     * @return Package
     */
    public static function createFromApi(ApiPackage $apiPackage, array $repoGitHubData): self
    {
        return new self($apiPackage, $repoGitHubData);
    }

    /**
     * Take the Type of the Downloads (total, monthly or daily).
     *
     * @param string $type
     *
     * @return string
     */
    public function getPackageDownloads(string $type): ?string
    {
        $statsType = 'get'.ucfirst($type);
        if (($download = $this->getDownloads()) && $download instanceof ApiPackage\Downloads) {
            return $download->{$statsType}();
        }
    }

    /**
     * Set the latest Stable and the latest Unstable version from a Package.
     *
     * @return Package
     */
    private function calculateLatestVersions(): self
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
            if ('stable' === self::parseStability($currentVersionName)) {
                $functionName = 'Stable';
            }

            if (version_compare($versionNormalized, $this->{'getLatest'.$functionName.'VersionNormalized'}()) > 0) {
                $this->{'setLatest'.$functionName.'Version'}($currentVersionName);
                $this->{'setLatest'.$functionName.'VersionNormalized'}($versionNormalized);

                $license = $version->getLicense();
                if (\is_array($license) && \count($license) > 0) {
                    $license = implode(',', $license);
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
    private function getBranchAliases(ApiPackage\Version $version): ?array
    {
        $extra = $version->getExtra();
        if (null !== $extra && isset($extra['branch-alias']) && \is_array($extra['branch-alias'])) {
            return $extra['branch-alias'];
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
    public static function parseStability(string $version): string
    {
        $version = preg_replace('{#.+$}i', '', $version);

        if ('dev-' === substr($version, 0, 4) || '-dev' === substr($version, -4)) {
            return 'dev';
        }

        preg_match('{'.self::$modifierRegex.'$}i', strtolower($version), $match);
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

    /**
     * @return null|string
     */
    public function getLicense(): ?string
    {
        return $this->license;
    }

    /**
     * @return null|string
     */
    public function getLatestStableVersion(): ?string
    {
        return $this->latestStableVersion;
    }

    /**
     * @return bool
     */
    public function hasStableVersion(): bool
    {
        return null !== $this->latestStableVersion;
    }

    /**
     * @return null|string
     */
    public function getLatestUnstableVersion(): ?string
    {
        return $this->latestUnstableVersion;
    }

    /**
     * @return null|string
     */
    public function getLatestStableVersionNormalized(): ?string
    {
        return $this->latestStableVersionNormalized;
    }

    /**
     * @return null|string
     */
    public function getLatestUnstableVersionNormalized(): ?string
    {
        return $this->latestUnstableVersionNormalized;
    }

    /**
     * @return bool
     */
    public function hasUnstableVersion(): bool
    {
        return null !== $this->latestUnstableVersion;
    }

    /**
     * @return ApiPackage
     */
    public function getOriginalObject(): ApiPackage
    {
        return $this->originalObject;
    }

    /**
     * @param string $latestUnstableVersionNormalized
     */
    private function setLatestUnstableVersionNormalized(string $latestUnstableVersionNormalized): void
    {
        $this->latestUnstableVersionNormalized = $latestUnstableVersionNormalized;
    }

    /**
     * @param string $license
     */
    private function setLicense(string $license): void
    {
        $this->license = $license;
    }

    /**
     * @param string $version
     */
    private function setLatestStableVersion(string $version): void
    {
        $this->latestStableVersion = $version;
    }

    /**
     * @param string $version
     */
    private function setLatestUnstableVersion(string $version): void
    {
        $this->latestUnstableVersion = $version;
    }

    /**
     * @param string $latestStableVersionNormalized
     */
    private function setLatestStableVersionNormalized(string $latestStableVersionNormalized): void
    {
        $this->latestStableVersionNormalized = $latestStableVersionNormalized;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getOriginalObject()->getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->getOriginalObject()->getDescription();
    }

    /**
     * @return ApiPackage\Downloads
     */
    public function getDownloads(): ApiPackage\Downloads
    {
        return $this->getOriginalObject()->getDownloads();
    }

    /**
     * @return string
     */
    public function getFavers(): string
    {
        return $this->getOriginalObject()->getFavers();
    }

    /**
     * @return array
     */
    public function getMaintainers(): array
    {
        return $this->getOriginalObject()->getMaintainers();
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->getOriginalObject()->getTime();
    }

    /**
     * @return string
     */
    public function getRepository(): string
    {
        return $this->getOriginalObject()->getRepository();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->getOriginalObject()->getType();
    }

    /**
     * @return ApiPackage\Version[]
     */
    public function getVersions(): array
    {
        return $this->getOriginalObject()->getVersions();
    }

    /**
     * @return int
     */
    public function getDependents(): int
    {
        return $this->getOriginalObject()->getDependents();
    }

    /**
     * @return int
     */
    public function getSuggesters(): int
    {
        return $this->getOriginalObject()->getSuggesters();
    }

    /**
     * @param ApiPackage $originalObject
     */
    private function setOriginalObject(ApiPackage $originalObject): void
    {
        $this->originalObject = $originalObject;
    }

    /**
     * @return string
     */
    public function getDefaultBranch(): string
    {
        return $this->defaultBranch;
    }
}
