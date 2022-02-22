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
use App\Badge\Model\UseCase\CreateCircleCiBadge;
use App\Badge\Service\ImageFactory;
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
     */
    public function status(
        Request $request,
        Poser $poser,
        ImageFactory $imageFactory,
        CreateCircleCiBadge $circleCiBadge,
        string $repository,
        string $branch = 'master',
        string $format = Badge::DEFAULT_FORMAT,
        string $style = Badge::DEFAULT_STYLE,
    ): Response {
        $style = $this->checkStyle($request, $poser, $style);

        return $this->serveBadge(
            $imageFactory,
            $circleCiBadge->createCircleCiBadge($repository, $branch, $format, $style)
        );
    }
}
