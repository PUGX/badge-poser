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

    /**
     * @param $repository
     * @param $subject
     * @param $color
     * @param string $format
     * @param null $context
     * @return Badge
     * @throws InvalidArgumentException
     */
    protected function createBadgeFromRepository($repository, $subject, $color, $format = 'svg', $context = null): Badge
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

    /**
     * @param $repository
     * @return Package
     * @throws UnexpectedValueException
     */
    protected function fetchPackage($repository): Package
    {
       return $this->packageRepository->fetchByRepository($repository);
    }

    /**
     * @param $subject
     * @param $status
     * @param $color
     * @param $format
     * @return Badge
     * @throws InvalidArgumentException
     */
    protected function createBadge($subject, $status, $color, $format): Badge
    {
        return new Badge($subject, $status, $color, $format);
    }

    /**
     * @param $package
     * @param null $context
     * @return mixed
     */
    abstract protected function prepareText($package, $context = null);
}
