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
     */
    public function status(
        Request $request,
        ImageFactory $imageFactory,
        CreateCircleCiBadge $circleCiBadge,
        $repository,
        $branch = 'master',
        $format = 'svg'
    ): Response {
        if ('plastic' === $request->query->get('format')) {
            $format = 'plastic';
        }

        return $this->serveBadge(
            $imageFactory,
            $circleCiBadge->createCircleCiBadge($repository, $branch, $format)
        );
    }
}
