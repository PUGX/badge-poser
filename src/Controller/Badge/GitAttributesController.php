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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GitAttributesController.
 *
 * @author Raphael Stolt <raphael.stolt@gmail.com>
 * @author Andrea Giannantonio <a.giannantonio@gmail.com>
 */
class GitAttributesController extends Controller
{
    /**
     * .gitAttributes action.
     *
     * @param Request $request
     * @param CreateGitAttributesBadge $createGitAttributesBadge
     * @param ImageFactory $imageFactory
     * @param string $repository repository
     * @param string $format
     * @return Response
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function gitAttributesAction(
        Request $request,
        CreateGitAttributesBadge $createGitAttributesBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ): Response {
        if ($request->query->get('format') === 'plastic') {
            $format = 'plastic';
        }

        $badge = $createGitAttributesBadge->createGitAttributesBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
