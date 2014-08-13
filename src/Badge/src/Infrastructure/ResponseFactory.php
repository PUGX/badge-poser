<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PUGX\Badge\Infrastructure;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PUGX\Badge\Model\Image;

/**
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class ResponseFactory
{
    public static function createFromImage(Image $image, $status, $maxage = 3600, $smaxage = 3600)
    {
        $response = new Response((string) $image, $status);

        $response->headers->set('Content-Type', 'image/svg+xml;charset=utf-8');
        $contentDisposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $image->getOutputFileName());
        $response->headers->set('Content-Disposition', $contentDisposition);

        $response->setMaxAge($maxage);
        $response->setSharedMaxAge($smaxage);

        return $response;
    }
}
