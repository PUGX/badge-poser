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

use Symfony\Component\HttpClient\HttpClient;
use PUGX\Badge\Model\PackageRepositoryInterface;

/**
 * Create the 'license' image using a generator `Poser`
 */
class CreateComposerLockBadge extends BaseCreatePackagistImage
{
    const COLOR_COMMITTED   = 'e60073';
    const COLOR_UNCOMMITTED = '99004d';
    const COLOR_ERROR       = 'aa0000';
    const LOCK_COMMITTED    = 'committed';
    const LOCK_UNCOMMITTED  = 'uncommitted';
    const LOCK_ERROR        = 'checking';
    const SUBJECT           = '.lock';
    const SUBJECT_ERROR     = 'Error';

    protected $text = self::LOCK_ERROR;

    /**
     * @param PackageRepositoryInterface $packageRepository
     */
    public function __construct(PackageRepositoryInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
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

        $client = HttpClient::create();
        $response = $client->request('HEAD',$repo . '/blob/master/composer.lock', array(
                'timeout'         => 2,
                'connect_timeout' => 1,
                'exceptions'      => false,
            )
        );

        $status = $response->getStatusCode();

        $this->text = self::LOCK_ERROR;
        $color      = self::COLOR_ERROR;
        $subject    = self::SUBJECT_ERROR;
        if (200 === $status) {
            $this->text = self::LOCK_COMMITTED;
            $color      = self::COLOR_COMMITTED;
            $subject    = self::SUBJECT;
        } elseif (404 === $status) {
            $this->text = self::LOCK_UNCOMMITTED;
            $color      = self::COLOR_UNCOMMITTED;
            $subject    = self::SUBJECT;
        }

        return $this->createBadgeFromRepository($repository, $subject, $color, $format);
    }

    protected function prepareText($package, $context = null)
    {
        return $this->text;
    }
}
