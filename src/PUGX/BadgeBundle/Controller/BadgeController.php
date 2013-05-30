<?php

/*
 * This file is part of the badge-poser package
 *
 * (c) Giulio De Donato <liuggio@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use PUGX\BadgeBundle\Service\ImageCreator;
use PUGX\BadgeBundle\Exception\InvalidArgumentException;

class BadgeController extends ContainerAware
{
    public function downloadsAction($repository, $type = 'total')
    {
        $imageCreator = $this->container->get('image_creator');
        $outputFilename = sprintf('%s.png', $type);
        $httpCode = 500;

        // get the statistic from packagist
        try {
            $downloads = $this->container->get('badger')->getPackageDownloads($repository, $type);
            $downloadsText = $imageCreator->transformNumberToReadableFormat($downloads);
            $httpCode = 200;
        } catch (InvalidArgumentException $e) {
            $downloadsText = ImageCreator::ERROR_TEXT_GENERIC;
        } catch (\Exception $e){
            $downloadsText = ImageCreator::ERROR_TEXT_CLIENT_EXCEPTION;
        }

        $image = $imageCreator->createDownloadsImage($downloadsText);
        return $this->streamImage($image, $outputFilename);
    }

    public function versionAction($repository)
    {
        $imageCreator = $this->container->get('image_creator');
        $outputFilename = sprintf('%s.png', 'version');

        $version = $this->container->get('badger')->getLatestStableVersion($repository);

        if ($version) {
            $image = $imageCreator->createStableImage($version);
        } else {
            $image = $imageCreator->createUnstableImage();
        }

        return $this->streamImage($image, $outputFilename);
    }

    protected function streamImage($image, $outputFilename)
    {
        $imageCreator = $this->container->get('image_creator');

        //generating the streamed response
        $response = new StreamedResponse(null);
        $response->setCallback(function () use ($imageCreator, $image) {
            $imageCreator->streamRawImageData($image);
        });

        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$outputFilename.'"');
        $response->send();

        $imageCreator->destroyImage($image);

        return $response;
    }
}
