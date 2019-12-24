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
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class BaseCreatePackagistImage.
 */
abstract class BaseCreatePackagistImage
{
    /** @var PackageRepositoryInterface */
    protected $packageRepository;

    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    /**
     * @param string|null $context
     *
     * @throws InvalidArgumentException
     */
    protected function createBadgeFromRepository(string $repository, string $subject, string $color, string $format = 'svg', $context = null): Badge
    {
        try {
            $package = $this->fetchPackage($repository);
            $text = $this->prepareText($package, $context);
        } catch (\Exception $e) {
            return $this->createDefaultBadge($format);
        }

        return $this->createBadge($subject, $text, $color, $format);
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
    protected function createBadge(string $subject, string $status, string $color, string $format): Badge
    {
        return new Badge($subject, $status, $color, $format);
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function createDefaultBadge(string $format): Badge
    {
        $subject = ' - ';
        $text = ' - ';
        $color = '7A7A7A';

        return $this->createBadge($subject, $text, $color, $format);
    }

    /**
     * @param string|null $context
     *
     * @return mixed
     */
    abstract protected function prepareText(Package $package, $context = null);
}
