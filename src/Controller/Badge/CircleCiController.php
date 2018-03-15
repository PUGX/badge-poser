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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

/**
 * Class CircleCiController
 * CircleCi action for badges.
 * @package App\Controller\Badge
 */
class CircleCiController extends Controller
{
    /**
     * CircleCi action.
     *
     * @param Request $request
     * @param ImageFactory $imageFactory
     * @param CreateCircleCiBadge $circleCiBadge
     * @param string $repository
     * @param string $branch
     * @param string $format
     * @return Response
     * @throws \InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     */
    public function status(
        Request $request,
        ImageFactory $imageFactory,
        CreateCircleCiBadge $circleCiBadge,
        $repository,
        $branch = 'master',
        $format = 'svg'
    ): Response {
        if ($request->query->get('format') === 'plastic') {
            $format = 'plastic';
        }

        $badge = $circleCiBadge->createCircleCiBadge($repository, $branch, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
