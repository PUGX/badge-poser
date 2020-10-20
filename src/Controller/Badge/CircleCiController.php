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

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateCircleCiBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CircleCiController
 * CircleCi action for badges.
 */
class CircleCiController extends AbstractController
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

        $badge = $circleCiBadge->createCircleCiBadge($repository, $branch, $format);
        $image = $imageFactory->createFromBadge($badge);

        $maxage = 60 * 60;
        $smaxage = 60 * 60;

        return ResponseFactory::createFromImage($image, 200, $maxage, $smaxage);
    }
}
