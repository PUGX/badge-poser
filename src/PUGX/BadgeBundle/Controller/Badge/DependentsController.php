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

/**
 * Class DependentsController.
 * dependents action for badges.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 * @author Andrea Giuliano <giulianoand@gmail.com>
 */
class DependentsController extends ContainerAware
{
    /**
     * Dependents action.
     *
     * @param string $repository repository
     * @param string $format
     *
     * @Route("/{repository}/dependents",
     *     name         = "pugx_badge_dependents",
     *     requirements = {
     *          "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *       }
     *     )
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @todo: remove container
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dependentsAction(Request $request, $repository, $format = 'svg')
    {
        $this->useCase = $this->container->get('use_case_badge_dependents');
        $this->imageFactory = $this->container->get('image_factory');

        if (in_array($request->query->get('format'), $this->container->get('poser')->validFormats())) {
            $format = $request->query->get('format');
        }

        $badge = $this->useCase->createDependentsBadge($repository, $format);

        $image = $this->imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
