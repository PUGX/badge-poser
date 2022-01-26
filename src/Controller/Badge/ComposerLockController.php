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

use App\Badge\Model\UseCase\CreateComposerLockBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Badge;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

/**
 * Composer-lock action for badges.
 */
final class ComposerLockController extends AbstractBadgeController
{
    /**
     * ComposerLock action.
     *
     * @param string $repository repository
     * @param string $format
     * @param string $style
     *
     * @throws \InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function composerLock(
        Request $request,
        Poser $poser,
        ImageFactory $imageFactory,
        CreateComposerLockBadge $composerLockBadge,
        $repository,
        $format = 'svg',
        $style = 'flat',
    ): Response {
        if (!\in_array($request->query->get('style'), $poser->validStyles(), true)) {
            $style = Badge::DEFAULT_STYLE;
        }

        return $this->serveBadge(
            $imageFactory,
            $composerLockBadge->createComposerLockBadge($repository, $format, $style)
        );
    }
}
