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

use App\Badge\Model\CacheableBadge;
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Service\NormalizerInterface;
use App\Badge\Service\TextNormalizer;

/**
 * Create the 'suggesters' image with the standard Font and standard Image.
 */
class CreateSuggestersBadge extends BaseCreatePackagistImage
{
    public const COLOR = '007ec6';
    public const SUBJECT = 'suggesters';

    private const TTL_DEFAULT_MAXAGE = CacheableBadge::TTL_ONE_HOUR;
    private const TTL_DEFAULT_SMAXAGE = CacheableBadge::TTL_ONE_DAY;

    private NormalizerInterface $normalizer;

    public function __construct(
        PackageRepositoryInterface $packageRepository,
        ?NormalizerInterface $textNormalizer = null
    ) {
        parent::__construct($packageRepository);
        $this->normalizer = $textNormalizer ?? new TextNormalizer();
    }

    public function createSuggestersBadge(string $repository, string $format = 'svg'): CacheableBadge
    {
        return $this->createBadgeFromRepository(
            $repository,
            self::SUBJECT,
            self::COLOR,
            $format,
            null,
            self::TTL_DEFAULT_MAXAGE,
            self::TTL_DEFAULT_SMAXAGE
        );
    }

    protected function prepareText(Package $package, ?string $context): string
    {
        $suggesters = $package->getSuggesters();
        if (0 === $suggesters) {
            return '0';
        }

        return $this->normalizer->normalize($suggesters);
    }
}
