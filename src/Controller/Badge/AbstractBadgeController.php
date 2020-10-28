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
use App\Badge\Model\CacheableBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractBadgeController.
 */
class AbstractBadgeController extends AbstractController
{
    public function serveBadge(ImageFactory $imageFactory, CacheableBadge $cacheableBadge): Response
    {
        return ResponseFactory::createFromImage(
            $imageFactory->createFromBadge($cacheableBadge),
            Response::HTTP_OK,
            $cacheableBadge->getMaxage(),
            $cacheableBadge->getSMaxAge()
        );
    }
}
