<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class PngBadgeController is deprecated see BadgeController
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 *
 * @deprecated
 */
class PngBadgeController extends Controller
{
    /**
     * Png Downloads action.
     *
     * @param string $repository repository
     * @param string $type       badge type
     *
     * @Route("/{repository}/downloads.png",
     *     name         = "pugx_badge_download_png",
     *     requirements = {
     *          "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *       }
     *     )
     * @Method({"GET"})
     *
     * @return RedirectResponse
     */
    public function downloadsTotalPNGAction($repository, $type = 'total')
    {
        return $this->redirect($this->generateUrl('pugx_badge_download', array('repository' => $repository)), 301);
    }

    /**
     * Downloads action.
     *
     * @param $repository
     * @param string $type
     *
     * @Route("/{repository}/d/{type}.png",
     *     name         = "pugx_badge_download_type_png",
     *     defaults     = {"type" = "total"},
     *     requirements = {
     *         "type"       = "total|daily|monthly",
     *         "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *         }
     *     )
     * @Method({"GET"})
     *
     * @return RedirectResponse
     */
    public function downloadsPNGAction($repository, $type = 'total')
    {
        return $this->redirect($this->generateUrl('pugx_badge_download_type', array('repository' => $repository, 'type' => $type)), 301);
    }

    /**
     * Version action.
     *
     * @param string $repository repository
     * @param string $latest     latest
     *
     * @Route("/{repository}/version.png",
     *     name="pugx_badge_version_png",
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     *
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return StreamedResponse
     */
    public function versionPngAction($repository, $latest = 'stable')
    {
        return $this->redirect($this->generateUrl('pugx_badge_version', array('repository' => $repository)), 301);
    }
    /**
     * Version action.
     *
     * @param string $repository repository
     * @param string $latest     latest
     *
     * @Route("/{repository}/v/{latest}.png",
     *     name         = "pugx_badge_version_latest_png",
     *     defaults     = {"latest" = "stable"},
     *     requirements = {
     *         "type"       = "stable|unstable",
     *         "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *         }
     *     )
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return StreamedResponse
     */
    public function versionPngLatestAction($repository, $latest = 'stable')
    {
        return $this->redirect($this->generateUrl('pugx_badge_version_latest', array('repository' => $repository, 'latest' => $latest)), 301);
    }

    /**
     * License action.
     *
     * @param string $repository repository
     *
     * @Route("/{repository}/license.png",
     *     name="pugx_badge_license_png",
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     *
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return StreamedResponse
     */
    public function licensePngAction($repository)
    {
        return $this->redirect($this->generateUrl('pugx_badge_license', array('repository'=>$repository)), 301);
    }
}
