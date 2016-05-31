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

use Guzzle\Http\Client;
use PUGX\Badge\Model\PackageRepositoryInterface;

/**
 * Create the 'license' image using a generator `Poser`
 */
class CreateComposerLockBadge extends BaseCreatePackagistImage
{
    const COLOR_COMMITED   = 'e60073';
    const COLOR_UNCOMMITED = '99004d';
    const COLOR_ERROR      = 'aa0000';
    const LOCK_COMMITED    = 'commited';
    const LOCK_UNCOMMITED  = 'uncommited';
    const LOCK_ERROR       = 'checking';
    const SUBJECT          = '.lock';
    const SUBJECT_ERROR    = 'Error';

    protected $text = self::LOCK_ERROR;


    /** @var Client */
    protected $client;

    /**
     * @param PackageRepositoryInterface $packageRepository
     * @param Client $client
     */
    public function __construct(PackageRepositoryInterface $packageRepository, Client $client)
    {
        $this->packageRepository = $packageRepository;
        $this->client = $client;
    }
    /**
     * @param string $repository
     * @param string $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createComposerLockBadge($repository, $format = 'svg')
    {
        $repo = str_replace('.git', '', $this->packageRepository
            ->fetchByRepository($repository)
            ->getOriginalObject()
            ->getRepository()
        );

        $request = $this->client->head(
            $repo . '/blob/master/composer.lock',
            array(),
            array(
                'timeout'         => 2,
                'connect_timeout' => 1,
                'exceptions'      => false,
            )
        );

        $response = $this->client->send($request);
        $status = 500;
        if ($request) {
            $status = $response->getStatusCode();
        }

        $this->text = self::LOCK_ERROR;
        $color      = self::COLOR_ERROR;
        $subject    = self::SUBJECT_ERROR;
        if (200 === $status) {
            $this->text = self::LOCK_COMMITED;
            $color      = self::COLOR_COMMITED;
            $subject    = self::SUBJECT;
        } elseif (404 === $status) {
            $this->text = self::LOCK_UNCOMMITED;
            $color      = self::COLOR_UNCOMMITED;
            $subject    = self::SUBJECT;
        }

        return $this->createBadgeFromRepository($repository, $subject, $color, $format);
    }

    protected function prepareText($package, $context = null)
    {
        return $this->text;
    }
}
