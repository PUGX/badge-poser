<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Model\UseCase;

use PUGX\Badge\Service\TextNormalizer;
use PUGX\Badge\Service\NormalizerInterface;
use PUGX\Badge\Model\PackageRepositoryInterface;

/**
 * Create the 'downloads' image with the standard Font and standard Image.
 */
class CreateDownloadsBadge extends BaseCreatePackagistImage
{
    CONST COLOR = '007ec6';
    CONST SUBJECT = 'downloads';

    /** @var \PUGX\Badge\Service\TextNormalizer */
    private $normalizer;

    /**
     * @param PackageRepositoryInterface $packageRepository
     * @param NormalizerInterface        $textNormalizer
     */
    public function __construct(PackageRepositoryInterface $packageRepository, NormalizerInterface $textNormalizer = null)
    {
        parent::__construct($packageRepository);
        $this->normalizer = $textNormalizer;

        if (!$this->normalizer) {
            $this->normalizer = new TextNormalizer();
        }
    }

    /**
     * @param $repository
     * @param $type
     * @param $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createDownloadsBadge($repository, $type, $format)
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format, $type);
    }

    protected function prepareText($package, $context = null)
    {
        $text = $this->normalizer->normalize($package->getPackageDownloads($context));
        $when = '';
        if ('daily' === $context) {
            $when = 'today';
        } elseif ('monthly' === $context) {
            $when = 'this month';
        }

        return sprintf("%s %s", $text, $when);
    }
}
