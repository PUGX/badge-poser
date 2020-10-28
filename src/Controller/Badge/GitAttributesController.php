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

use App\Badge\Model\UseCase\CreateGitAttributesBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GitAttributesController.
 */
class GitAttributesController extends AbstractBadgeController
{
    /**
     * .gitAttributes action.
     *
     * @param string $repository repository
     * @param string $format
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

        return $this->serveBadge(
            $imageFactory,
            $createGitAttributesBadge->createGitAttributesBadge($repository, $format)
        );
    }
}
