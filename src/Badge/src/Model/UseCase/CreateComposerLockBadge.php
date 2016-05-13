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

/**
 * Create the 'license' image using a generator `Poser`
 */
class CreateComposerLockadge extends BaseCreatePackagistImage
{
    const COLOR_COMMITED   = '28a3df';
    const COLOR_UNCOMMITED = 'e68718';
    const LOCK_COMMITED    = 'committed';
    const SUBJECT          = '.lock';
    const LOCK_UNCOMMITED  = 'uncommited';

    protected $text = self::LOCK_UNCOMMITED;
    /**
     * @param string $repository
     * @param string $format
     *
     * @return \PUGX\Badge\Model\Badge
     */
    public function createComposerLockBadge($repository, $format = 'svg')
    {
        $repo = str_replace('.git', '', $repository->getOriginalObject()->getRepository());

        $client = new Client();
        $response = $client->get($repo . '/blob/master/composer.lock');

        $this->text = self::LOCK_UNCOMMITED;
        $color      = self::COLOR_UNCOMMITED;
        if ($response->getResponse()->getStatusCode() != 200) {
            $this->text = self::LOCK_COMMITED;
            $color      = self::COLOR_COMMITED;
        }

        return $this->createBadgeFromRepository($repository, self::SUBJECT, $color, $format);
    }

    protected function prepareText($package, $context = null)
    {
        return $this->text;
    }
}
