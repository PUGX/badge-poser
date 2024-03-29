<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Badge\Infrastructure;

use App\Badge\Model\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class ResponseFactory
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function createFromImage(Image $image, int $status, int $maxage = 3600, int $smaxage = 3600): Response
    {
        $response = new Response((string) $image, $status);

        $response->headers->set('Content-Type', 'image/svg+xml;charset=utf-8');
        $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $image->getOutputFileName());
        $response->headers->set('Content-Disposition', $contentDisposition);

        $response->setMaxAge($maxage);
        $response->setSharedMaxAge($smaxage);
        $response->setPublic();

        return $response;
    }
}
