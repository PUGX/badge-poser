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
 * Class DownloadsController.
 * Download action for badges.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 * @author Andrea Giuliano <giulianoand@gmail.com>
 */
class DownloadsController extends ContainerAware
{
    /**
     * Downloads action.
     *
     * @param string $repository repository
     * @param string $type       badge type
     * @param string $format
     *
     * @Route("/{repository}/downloads",
     *     name         = "pugx_badge_download",
     *     defaults     = {"type" = "total"},
     *     requirements = {
     *          "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *       }
     *     )
     * @Route("/{repository}/d/{type}",
     *     name         = "pugx_badge_download_type",
     *     requirements = {
     *         "type"       = "total|daily|monthly",
     *         "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *         }
     *     )
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @todo: remove container
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadsAction(Request $request, $repository, $type, $format = 'svg')
    {
        $this->useCase = $this->container->get('use_case_badge_downloads');
        $this->imageFactory = $this->container->get('image_factory');

        if (in_array($request->query->get('format'), $this->container->get('poser')->validFormats())) {
            $format = $request->query->get('format');
        }

        $badge = $this->useCase->createDownloadsBadge($repository, $type, $format);

        $image = $this->imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
