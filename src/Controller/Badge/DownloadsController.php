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

use App\Badge\Model\Badge;
use App\Badge\Model\UseCase\CreateDownloadsBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Download action for badges.
 */
final class DownloadsController extends AbstractBadgeController
{
    /**
     * @throws \InvalidArgumentException
     */
    public function downloads(
        Request $request,
        Poser $poser,
        CreateDownloadsBadge $createDownloadsBadge,
        ImageFactory $imageFactory,
        string $repository,
        string $type,
        string $format = Badge::DEFAULT_FORMAT,
        string $style = Badge::DEFAULT_STYLE,
    ): Response {
        $style = $this->checkStyle($request, $poser, $style);

        return $this->serveBadge(
            $imageFactory,
            $createDownloadsBadge->createDownloadsBadge($repository, $type, $format, $style),
        );
    }
}
