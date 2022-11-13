<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Badge;

use App\Badge\Model\Badge;
use App\Badge\Model\UseCase\CreateComposerLockBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Composer-lock action for badges.
 */
final class ComposerLockController extends AbstractBadgeController
{
    /**
     * ComposerLock action.
     *
     * @param string $repository repository
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function composerLock(
        Request $request,
        Poser $poser,
        ImageFactory $imageFactory,
        CreateComposerLockBadge $composerLockBadge,
        string $repository,
        string $format = Badge::DEFAULT_FORMAT,
        string $style = Badge::DEFAULT_STYLE,
    ): Response {
        $style = $this->checkStyle($request, $poser, $style);

        return $this->serveBadge(
            $imageFactory,
            $composerLockBadge->createComposerLockBadge($repository, $format, $style)
        );
    }
}
