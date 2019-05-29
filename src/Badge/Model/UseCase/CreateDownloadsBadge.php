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
use App\Badge\Model\Package;
use App\Badge\Model\PackageRepositoryInterface;
use App\Badge\Service\NormalizerInterface;
use App\Badge\Service\TextNormalizer;
use InvalidArgumentException;

/**
 * Class CreateDownloadsBadge
 * Create the 'downloads' image with the standard Font and standard Image.
 */
class CreateDownloadsBadge extends BaseCreatePackagistImage
{
    private const COLOR = '007ec6';
    private const SUBJECT = 'downloads';

    /** @var NormalizerInterface|null */
    private $normalizer;

    /**
     * @param PackageRepositoryInterface $packageRepository
     * @param NormalizerInterface|null   $textNormalizer
     */
    public function __construct(PackageRepositoryInterface $packageRepository, ?NormalizerInterface $textNormalizer = null)
    {
        parent::__construct($packageRepository);
        $this->normalizer = $textNormalizer;

        if (!$this->normalizer) {
            $this->normalizer = new TextNormalizer();
        }
    }

    /**
     * @param string $repository
     * @param string $type
     * @param string $format
     *
     * @return Badge
     *
     * @throws InvalidArgumentException
     */
    public function createDownloadsBadge(string $repository, string $type, string $format): Badge
    {
        return $this->createBadgeFromRepository($repository, self::SUBJECT, self::COLOR, $format, $type);
    }

    /**
     * @param Package     $package
     * @param string|null $context
     *
     * @return mixed|string
     *
     * @throws \InvalidArgumentException
     */
    protected function prepareText(Package $package, $context = null)
    {
        $text = $this->normalizer->normalize($package->getPackageDownloads($context));
        $when = '';
        if ('daily' === $context) {
            $when = 'today';
        } elseif ('monthly' === $context) {
            $when = 'this month';
        }

        return sprintf('%s %s', $text, $when);
    }
}
