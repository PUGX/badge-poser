<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Service;

use Guzzle\Http\ClientInterface;
use PUGX\BadgeBundle\Image;

/**
 * Class ShieldIOImageCreator, responsible to create an Image Object
 *
 * @author Claudio D'Alicandro <claudio.dalicandro@gmail.com>
 * @author Giulio De Donato <liuggio@gmail.com>
 */
class ShieldIOImageCreator implements ImageCreatorInterface
{
    private static $definedColors = array(
        self::DOWNLOADS => 'blue',
        self::STABLE => '28a3df',
        self::UNSTABLE => 'e68718',
        self::ERROR => 'red',
        self::LICENSE => '428F7E'
    );

    /** @var ClientInterface $httpClient */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    public function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Create the 'downloads' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return mixed
     */
    public function createDownloadsImage($value)
    {
        $response = $this->fetchResponse(self::DOWNLOADS, $value, self::$definedColors[self::DOWNLOADS]);

        return Image::createFromResponse($response);
    }

    /**
     * Create the 'stable:no release' image with the standard Font and stable image template.
     *
     * @param string $value
     *
     * @return \PUGX\BadgeBundle\ImageInterface
     */
    public function createStableNoImage($value)
    {
        $response = $this->fetchResponse(self::STABLE, $value, self::$definedColors[self::STABLE]);

        return Image::createFromResponse($response);
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createStableImage($value)
    {
        $response = $this->fetchResponse(self::STABLE, $value, self::$definedColors[self::STABLE]);

        return Image::createFromResponse($response);
    }

    /**
     * Create the 'stable' image with the standard Font and standard Image.
     *
     * @param string $value
     *
     * @return resource
     */
    public function createUnstableImage($value = '@dev')
    {
        $response = $this->fetchResponse(self::UNSTABLE, $value, self::$definedColors[self::UNSTABLE]);

        return Image::createFromResponse($response);
    }

    /**
     * Create the 'error' image
     *
     * @param string $value
     *
     * @return \PUGX\BadgeBundle\ImageInterface
     */
    public function createErrorImage($value)
    {
        $response = $this->fetchResponse(self::ERROR, $value, self::$definedColors[self::ERROR]);

        return Image::createFromResponse($response);
    }

    /**
     * Create a 'license' Image
     *
     * @param string $value
     *
     * @return \PUGX\BadgeBundle\ImageInterface
     */
    public function createLicenseImage($value)
    {
        $response = $this->fetchResponse(self::LICENSE, $value, self::$definedColors[self::LICENSE]);

        return Image::createFromResponse($response);
    }

    /**
     * @param $vendor
     * @param $value
     * @param $color
     *
     * @return string
     */
    private function generateURI($vendor, $value, $color)
    {
        $value = str_replace('-', '--', $value);
        $value = str_replace(' ', '_', $value);
        $value  = urlencode($value);

        return sprintf(
            'http://img.shields.io/badge/%s-%s-%s.svg',
            $vendor, $value, $color
        );
    }

    /**
     * @param $vendor
     * @param $value
     * @param $color
     * @return array|\Guzzle\Http\Message\Response
     */
    private function fetchResponse($vendor, $value, $color)
    {
        $request = $this->httpClient->get($this->generateURI($vendor, $value, $color));

        $response = $this->httpClient->send($request);

        return $response;
    }
}
