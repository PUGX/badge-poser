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
use App\Badge\Model\PackageRepositoryInterface;

abstract class BaseCreatePackagistImage
{
    /** @var PackageRepositoryInterface */
    protected $packageRepository;

    /**
     * @param PackageRepositoryInterface $packageRepository
     */
    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    protected function createBadgeFromRepository($repository, $subject, $color, $format = 'svg', $context = null)
    {
        try{
            $package = $this->fetchPackage($repository);
            $text = $this->prepareText($package, $context);
        }catch(\Exception $e) {
            $subject = ' - ';
            $text = ' - ';
            $color = '7A7A7A';
        }

        return $this->createBadge($subject, $text, $color, $format);
    }

    protected function fetchPackage($repository)
    {
       return $this->packageRepository->fetchByRepository($repository);
    }

    protected function createBadge($subject, $status, $color, $format)
    {
        return new Badge($subject, $status, $color, $format);
    }

    abstract protected function prepareText($package, $context = null);
}
