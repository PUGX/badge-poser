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
use App\Badge\Service\NormalizerInterface;
use App\Badge\Service\TextNormalizer;

/**
 * Create the 'downloads' image with the standard Font and standard Image.
 */
final class CreateDownloadsBadge extends BaseCreatePackagistImage
{
    private const string COLOR = '007ec6';
    private const string SUBJECT = 'downloads';

    private const int TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const int TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_HOUR;

    private NormalizerInterface $normalizer;

    public function __construct(PackageRepositoryInterface $packageRepository, ?NormalizerInterface $textNormalizer = null)
    {
        parent::__construct($packageRepository);
        $this->normalizer = $textNormalizer ?? new TextNormalizer();
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function createDownloadsBadge(string $repository, string $type, string $format = Badge::DEFAULT_FORMAT, string $style = Badge::DEFAULT_STYLE): CacheableBadge
    {
        $maxage = self::TTL_DEFAULT_MAXAGE;
        $smaxage = self::TTL_DEFAULT_SMAXAGE;

        $badge = $this->createBadgeFromRepository(
            $repository,
            self::SUBJECT,
            self::COLOR,
            $format,
            $style,
            $type,
            $maxage,
            $smaxage
        );

        $status = \trim($badge->getStatus());
        $order = \substr($status, -1);
        if ('k' === $order) {
            $maxage = CacheableBadge::TTL_ONE_HOUR;
            $smaxage = CacheableBadge::TTL_ONE_HOUR;
        } elseif ('M' === $order) {
            $maxage = CacheableBadge::TTL_SIX_HOURS;
            $smaxage = CacheableBadge::TTL_SIX_HOURS;
        } elseif ('G' === $order) {
            $maxage = CacheableBadge::TTL_SIX_HOURS;
            $smaxage = CacheableBadge::TTL_SIX_HOURS;
        } elseif ('T' === $order) {
            $maxage = CacheableBadge::TTL_SIX_HOURS;
            $smaxage = CacheableBadge::TTL_SIX_HOURS;
        }

        $badge->setMaxAge($maxage);
        $badge->setSMaxAge($smaxage);

        return $badge;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function prepareText(Package $package, ?string $context): string
    {
        $text = $this->normalizer->normalize($package->getPackageDownloads($context));
        $when = '';
        if ('daily' === $context) {
            $when = 'today';
        } elseif ('monthly' === $context) {
            $when = 'this month';
        }

        return \sprintf('%s %s', $text, $when);
    }
}
