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
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class CreateComposerLockBadge
 * Create the 'license' image using a generator `Poser`.
 */
class CreateComposerLockBadge extends BaseCreatePackagistImage
{
    private const COLOR_COMMITTED = 'e60073';
    private const COLOR_UNCOMMITTED = '99004d';
    private const COLOR_ERROR = 'aa0000';
    private const LOCK_COMMITTED = 'committed';
    private const LOCK_UNCOMMITTED = 'uncommitted';
    private const LOCK_ERROR = 'checking';
    private const SUBJECT = '.lock';
    private const SUBJECT_ERROR = 'Error';
    private const TIMEOUT_SECONDS = 8;
    private const CONNECT_TIMEOUT_SECONDS = 5;

    protected $text = self::LOCK_ERROR;

    /** @var ClientInterface */
    protected $client;

    public function __construct(PackageRepositoryInterface $packageRepository, ClientInterface $client)
    {
        parent::__construct($packageRepository);
        $this->client = $client;
    }

    /**
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createComposerLockBadge(string $repository, string $format = 'svg'): Badge
    {
        try {
            /** @var Package $package */
            $package = $this->fetchPackage($repository);
            $repo = str_replace('.git', '', $package->getRepository());
        } catch (\Exception $e) {
            return $this->createDefaultBadge($format);
        }

        $response = $this->client->request(
            'HEAD',
            $repo.'/blob/'.$package->getDefaultBranch().'/composer.lock',
            [
                RequestOptions::TIMEOUT => self::TIMEOUT_SECONDS,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT_SECONDS,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );

        $status = 500;
        if (null !== $response) {
            $status = $response->getStatusCode();
        }

        $this->text = self::LOCK_ERROR;
        $color = self::COLOR_ERROR;
        $subject = self::SUBJECT_ERROR;
        if (200 === $status) {
            $this->text = self::LOCK_COMMITTED;
            $color = self::COLOR_COMMITTED;
            $subject = self::SUBJECT;
        } elseif (404 === $status) {
            $this->text = self::LOCK_UNCOMMITTED;
            $color = self::COLOR_UNCOMMITTED;
            $subject = self::SUBJECT;
        }

        return $this->createBadgeFromRepository($repository, $subject, $color, $format);
    }

    /**
     * @param string|null $context
     */
    protected function prepareText(Package $package, $context = null): string
    {
        return $this->text;
    }
}
