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
 * Class LicenseController.
 * License action for badges.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 * @author Andrea Giuliano <giulianoand@gmail.com>
 */
class VersionController extends ContainerAware
{
    /**
     * Version action.
     *
     * @param string $repository repository
     * @param string $latest     latest
     *
     * @Route("/{repository}/version",
     *     name="pugx_badge_version",
     *     defaults     = {"latest" = "stable"},
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     * @Route("/{repository}/v/{latest}",
     *     name         = "pugx_badge_version_latest",
     *     requirements = {
     *         "latest"     = "stable|unstable",
     *         "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *         }
     *     )
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return Response
     */
    public function versionAction(Request $request, $repository, $latest, $format='svg')
    {
        if ($request->query->get('format') == 'plastic') {
            $format = 'plastic';
        }

        $this->useCase = $this->container->get('use_case_badge_version');
        $this->imageFactory = $this->container->get('image_factory');

        $function = 'create'.ucfirst($latest).'Badge';

        $badge = $this->useCase->{$function}($repository, $format);
        $image = $this->imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
