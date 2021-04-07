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

use App\Badge\Model\UseCase\CreateDownloadsBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DownloadsController
 * Download action for badges.
 */
class DownloadsController extends AbstractBadgeController
{
    /**
     * Downloads action.
     *
     * @param string $repository repository
     * @param string $type       badge type
     * @param string $format
     *
     * @throws \InvalidArgumentException
     */
    public function downloads(
        Request $request,
        Poser $poser,
        CreateDownloadsBadge $createDownloadsBadge,
        ImageFactory $imageFactory,
        $repository,
        $type,
        $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validStyles(), true)) {
            $format = (string) $request->query->get('format');
        }

        return $this->serveBadge(
            $imageFactory,
            $createDownloadsBadge->createDownloadsBadge($repository, $type, $format)
        );
    }
}
