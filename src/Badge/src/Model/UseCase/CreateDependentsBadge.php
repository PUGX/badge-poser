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
 * Create the 'dependents' image with the standard Font and standard Image.
 */
class CreateDependentsBadge extends BaseCreatePackagistImage
{
    CONST COLOR = '007ec6';
    CONST SUBJECT = 'dependents';

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
     * @param $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createDependentsBadge($repository, $format)
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format);
    }

    protected function prepareText($package, $context = null)
    {
        return $this->normalizer->normalize($package->getDependents($context));
    }
}
