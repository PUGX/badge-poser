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
    private static string $modifierRegex = '[._-]?(?:(stable|beta|b|RC|alpha|a|patch|pl|p)(?:[.-]?(\d+))?)?([.-]?dev)?';
    private string $license;
    private ApiPackage $originalObject;
    private ?string $latestStableVersion = null;
    private ?string $latestUnstableVersion = null;
    private ?string $latestStableVersionNormalized = null;
    private ?string $latestUnstableVersionNormalized = null;
    private string $defaultBranch;

    private function __construct(ApiPackage $apiPackage, array $repoGitHubData)
    {
        $this->setOriginalObject($apiPackage);
        $this->calculateLatestVersions();
        $this->defaultBranch = $repoGitHubData['default_branch'];
    }

    /**
     * Create a new Package decorated with the Api Package.
     */
    public static function createFromApi(ApiPackage $apiPackage, array $repoGitHubData): self
    {
        return new self($apiPackage, $repoGitHubData);
    }

    /**
     * Take the Type of the Downloads (total, monthly or daily).
     */
    public function getPackageDownloads(string $type): ?string
    {
        $statsType = 'get'.ucfirst($type);

        return $this->getDownloads()->{$statsType}();
    }

    /**
     * Set the latest Stable and the latest Unstable version from a Package.
     */
    private function calculateLatestVersions(): self
    {
        $versions = $this->getVersions();

        foreach ($versions as $name => $version) {
            $currentVersionName = $version->getVersion();
            $versionNormalized = $version->getVersionNormalized();

            $aliases = $this->getBranchAliases($version);
            if (null !== $aliases && \array_key_exists($currentVersionName, $aliases)) {
                $currentVersionName = $aliases[$currentVersionName];
            }

            $functionName = 'Unstable';
            if ('stable' === self::parseStability($currentVersionName)) {
                $functionName = 'Stable';
            }

            if (version_compare($versionNormalized, $this->{'getLatest'.$functionName.'VersionNormalized'}()) > 0) {
                $this->{'setLatest'.$functionName.'Version'}($currentVersionName);
                $this->{'setLatest'.$functionName.'VersionNormalized'}($versionNormalized);
                /** @var string|array $license */
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

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function getLatestStableVersion(): ?string
    {
        return $this->latestStableVersion;
    }

    public function hasStableVersion(): bool
    {
        return null !== $this->latestStableVersion;
    }

    public function getLatestUnstableVersion(): ?string
    {
        return $this->latestUnstableVersion;
    }

    public function getLatestStableVersionNormalized(): ?string
    {
        return $this->latestStableVersionNormalized;
    }

    public function getLatestUnstableVersionNormalized(): ?string
    {
        return $this->latestUnstableVersionNormalized;
    }

    public function hasUnstableVersion(): bool
    {
        return null !== $this->latestUnstableVersion;
    }

    public function getOriginalObject(): ApiPackage
    {
        return $this->originalObject;
    }

    private function setLatestUnstableVersionNormalized(string $latestUnstableVersionNormalized): void
    {
        $this->latestUnstableVersionNormalized = $latestUnstableVersionNormalized;
    }

    private function setLicense(string $license): void
    {
        $this->license = $license;
    }

    private function setLatestStableVersion(string $version): void
    {
        $this->latestStableVersion = $version;
    }

    private function setLatestUnstableVersion(string $version): void
    {
        $this->latestUnstableVersion = $version;
    }

    private function setLatestStableVersionNormalized(string $latestStableVersionNormalized): void
    {
        $this->latestStableVersionNormalized = $latestStableVersionNormalized;
    }

    public function getName(): string
    {
        return $this->getOriginalObject()->getName();
    }

    public function getDescription(): string
    {
        return $this->getOriginalObject()->getDescription();
    }

    public function getDownloads(): ApiPackage\Downloads
    {
        return $this->getOriginalObject()->getDownloads();
    }

    public function getFavers(): string
    {
        return $this->getOriginalObject()->getFavers();
    }

    public function getMaintainers(): array
    {
        return $this->getOriginalObject()->getMaintainers();
    }

    public function getTime(): string
    {
        return $this->getOriginalObject()->getTime();
    }

    public function getRepository(): string
    {
        return $this->getOriginalObject()->getRepository();
    }

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

    public function getDependents(): int
    {
        return $this->getOriginalObject()->getDependents();
    }

    public function getSuggesters(): int
    {
        return $this->getOriginalObject()->getSuggesters();
    }

    private function setOriginalObject(ApiPackage $originalObject): void
    {
        $this->originalObject = $originalObject;
    }

    public function getDefaultBranch(): string
    {
        return $this->defaultBranch;
    }
}
