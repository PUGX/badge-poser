<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\Badge\Image;

use Guzzle\Http\Message\Response;

/**
 * Class Image, an Image value Object
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class Image implements ImageInterface
{
    /**
     * @var \Guzzle\Http\Message\Response
     */
    private $response;

    /**
     * @param Response $response
     */
    private function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the image content as binary string
     */
    public function __toString()
    {
        return $this->response->getBody(true);
    }

    /**
     * Factory method
     *
     * @param Response $response
     *
     * @return Image
     */
    public static function createFromResponse(Response $response)
    {
        return new self($response);
    }
}
