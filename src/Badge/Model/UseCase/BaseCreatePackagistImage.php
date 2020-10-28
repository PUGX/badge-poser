<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Model\UseCase;

use App\Badge\Model\Badge;
use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class BaseCreatePackagistImage.
 */
abstract class BaseCreatePackagistImage
{
    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    protected PackageRepositoryInterface $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function createBadgeFromRepository(
        string $repository,
        string $subject,
        string $color,
        string $format = 'svg',
        ?string $context = null,
        int $maxage = self::TTL_DEFAULT_MAXAGE,
        int $smaxage = self::TTL_DEFAULT_SMAXAGE
    ): CacheableBadge {
        try {
            $package = $this->fetchPackage($repository);
            $text = $this->prepareText($package, $context);
        } catch (\Exception $e) {
            return $this->createDefaultBadge($format);
        }

        return $this->createBadge($subject, $text, $color, $format, $maxage, $smaxage);
    }

    /**
     * @throws UnexpectedValueException
     */
    protected function fetchPackage(string $repository): Package
    {
        return $this->packageRepository->fetchByRepository($repository);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function createBadge(
        string $subject,
        string $status,
        string $color,
        string $format,
        int $maxage = self::TTL_DEFAULT_MAXAGE,
        int $smaxage = self::TTL_DEFAULT_SMAXAGE
    ): CacheableBadge {
        return new CacheableBadge(
            new Badge($subject, $status, $color, $format),
            $maxage,
            $smaxage
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function createDefaultBadge(string $format): CacheableBadge
    {
        $subject = ' - ';
        $text = ' - ';
        $color = '7A7A7A';

        return $this->createBadge($subject, $text, $color, $format, CacheableBadge::TTL_NO_CACHE, CacheableBadge::TTL_NO_CACHE);
    }

    abstract protected function prepareText(Package $package, ?string $context): string;
}
