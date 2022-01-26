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
use PUGX\Poser\Badge;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GitAttributesController extends AbstractBadgeController
{
    /**
     * @throws \InvalidArgumentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function gitAttributes(
        Request $request,
        Poser $poser,
        CreateGitAttributesBadge $createGitAttributesBadge,
        ImageFactory $imageFactory,
        string $repository,
        string $format = 'svg',
        string $style = 'flat',
    ): Response {
        if (!\in_array($request->query->get('style'), $poser->validStyles(), true)) {
            $style = Badge::DEFAULT_STYLE;
        }

        return $this->serveBadge(
            $imageFactory,
            $createGitAttributesBadge->createGitAttributesBadge($repository, $format, $style)
        );
    }
}
