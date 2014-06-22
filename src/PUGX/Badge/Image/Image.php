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
     * @var string $content
     */
    private $content;

    /**
     * @param string $content
     */
    private function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Returns the image content as binary string
     */
    public function __toString()
    {
        return $this->content;
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
        $content = $response->getBody(true);

        return self::createFromString($content);
    }

    /**
     * Factory method
     *
     * @param string $content
     *
     * @return Image
     */
    public static function createFromString($content)
    {
        return new self($content);
    }
}
