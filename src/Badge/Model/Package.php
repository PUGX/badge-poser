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
use Packagist\Api\Result\Package\Maintainer;
use Packagist\Api\Result\Package\Version;

/**
 * Decorates the Packagist Package.
 */
final class Package
{
    private static string $modifierRegex = '[._-]?(?:(stable|beta|b|RC|alpha|a|patch|pl|p)(?:[.-]?(\d+))?)?([.-]?dev)?';
    private ?string $license = null;
    private ApiPackage $originalObject;
    private ?string $latestStableVersion = null;
    private ?string $latestUnstableVersion = null;
    private ?string $latestStableVersionNormalized = null;
    private ?string $latestUnstableVersionNormalized = null;
    private string $defaultBranch;

    /**
     * @param array{default_branch: string} $repoGitHubData
     */
    private function __construct(ApiPackage $apiPackage, array $repoGitHubData)
    {
        $this->setOriginalObject($apiPackage);
        $this->calculateLatestVersions();
        $this->defaultBranch = $repoGitHubData['default_branch'];
    }

    /**
     * Create a new Package decorated with the Api Package.
     *
     * @param array{default_branch: string} $repoGitHubData
     */
    public static function createFromApi(ApiPackage $apiPackage, array $repoGitHubData = ['default_branch' => '']): self
    {
        return new self($apiPackage, $repoGitHubData);
    }

    /**
     * Take the Type of the Downloads (total, monthly or daily).
     */
    public function getPackageDownloads(?string $type = 'total'): ?int
    {
        $statsType = 'get'.\ucfirst($type ?? 'total');

        return $this->getDownloads()?->{$statsType}();
    }

    private function comparator(Version $version1, Version $version2): int
    {
        return $version1->getTime() <=> $version2->getTime();
    }

    /**
     * Set the latest Stable and the latest Unstable version from a Package.
     */
    private function calculateLatestVersions(): void
    {
        $versions = $this->getVersions();

        \usort($versions, [$this, 'comparator']);

        foreach ($versions as $version) {
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

            $latestVersion = (string) $this->{'getLatest'.$functionName.'VersionNormalized'}();
            if (\version_compare($versionNormalized, $latestVersion) > 0) {
                $this->{'setLatest'.$functionName.'Version'}($currentVersionName);
                $this->{'setLatest'.$functionName.'VersionNormalized'}($versionNormalized);
                /** @var string|string[] $license */
                $license = $version->getLicenses();

                $this->setLicense($this->normalizeLicense($license));
            }
        }
    }

    /**
     * Get all the branch aliases.
     *
     * @return array<string, string>|null
     */
    private function getBranchAliases(Version $version): ?array
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
        $version = \preg_replace('{#.+$}i', '', $version) ?? '';

        if (\str_starts_with($version, 'dev-') || '-dev' === \substr($version, -4)) {
            return 'dev';
        }

        \preg_match('{'.self::$modifierRegex.'$}i', \strtolower($version), $match);
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

    public function getDownloads(): ApiPackage\Downloads|null
    {
        return $this->getOriginalObject()->getDownloads();
    }

    public function getFavers(): int
    {
        return $this->getOriginalObject()->getFavers();
    }

    /**
     * @return Maintainer[]
     */
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
     * @return Version[]
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

    /**
     * @param array<string>|string $licenseData
     */
    private function normalizeLicense(array|string $licenseData): string
    {
        if (!\is_array($licenseData)) {
            return $licenseData;
        }

        if (\count($licenseData) > 0) {
            return \implode(',', $licenseData);
        }

        return '';
    }

    public function getLatestRequire(string $require): string
    {
        $latestStableVersion = $this->getLatestStableVersion();

        if (null === $latestStableVersion) {
            return '';
        }

        /** @var Version $version */
        $version = $this->getVersions()[$latestStableVersion];
        $requires = $version->getRequire();

        return $requires[$require] ?? '';
    }
}
