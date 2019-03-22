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
use App\Badge\Model\UseCase\CreateGitAttributesBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GitAttributesController.
 */
class GitAttributesController extends AbstractController
{
    /**
     * .gitAttributes action.
     *
     * @param Request                  $request
     * @param CreateGitAttributesBadge $createGitAttributesBadge
     * @param ImageFactory             $imageFactory
     * @param string                   $repository               repository
     * @param string                   $format
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function gitAttributes(
        Request $request,
        CreateGitAttributesBadge $createGitAttributesBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ): Response {
        if ('plastic' === $request->query->get('format')) {
            $format = 'plastic';
        }

        $badge = $createGitAttributesBadge->createGitAttributesBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
