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

use App\Badge\Model\UseCase\CreateCircleCiBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Badge;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CircleCi action for badges.
 */
final class CircleCiController extends AbstractBadgeController
{
    /**
     * CircleCi action.
     *
     * @param string $repository
     * @param string $branch
     * @param string $format
     * @param string $style
     */
    public function status(
        Request $request,
        Poser $poser,
        ImageFactory $imageFactory,
        CreateCircleCiBadge $circleCiBadge,
        $repository,
        $branch = 'master',
        $format = 'svg',
        $style = 'flat',
    ): Response {
        if (!\in_array($request->query->get('style'), $poser->validStyles(), true)) {
            $style = Badge::DEFAULT_STYLE;
        }

        return $this->serveBadge(
            $imageFactory,
            $circleCiBadge->createCircleCiBadge($repository, $branch, $format, $style)
        );
    }
}
