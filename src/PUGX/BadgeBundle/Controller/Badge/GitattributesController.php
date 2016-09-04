<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Controller\Badge;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

use PUGX\Badge\Infrastructure\ResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GitattributesController.
 *
 * @author Raphael Stolt <raphael.stolt@gmail.com>
 */
class GitattributesController extends ContainerAware
{
    /**
     * .gitattributes action.
     *
     * @param string $repository repository
     *
     * @Route("/{repository}/gitattributes",
     *     name="pugx_badge_gitattributes",
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return Response
     */
    public function gitattributesAction(Request $request, $repository, $format='svg')
    {
        if ($request->query->get('format') == 'plastic') {
            $format = 'plastic';
        }

        $this->useCase = $this->container->get('use_case_badge_gitattributes');
        $this->imageFactory = $this->container->get('image_factory');


        $badge = $this->useCase->createGitattributesBadge($repository, $format);
        $image = $this->imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
