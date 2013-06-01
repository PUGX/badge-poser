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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use PUGX\BadgeBundle\Service\ImageCreator;
use PUGX\BadgeBundle\Exception\InvalidArgumentException;

class BadgeController extends ContainerAware
{
    /**
     * @Route("/{repository}/downloads.png",
     *     name         = "pugx_badge",
     *     requirements = {"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     * @Route("/{repository}/d/{type}.png",
     *     name         = "pugx_badge_stat",
     *     defaults     = {"type" = "total"},
     *     requirements = {
     *         "type"       = "total|daily|monthly",
     *         "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *         }
     *     )
     * @Method({"GET"})
     *
     * @param string $repository
     * @param string $type
     *
     * @return StreamedResponse
     */
    public function downloadsAction($repository, $type = 'total')
    {
        $imageCreator = $this->container->get('image_creator');
        $outputFilename = sprintf('%s.png', $type);
        $status = 500;

        try {
            $package = $this->container->get('package_manager')->fetchPackage($repository);
            $text = $this->container->get('package_manager')->getPackageDownloads($package, $type);
            $status = 200;
        } catch (UnexpectedValueException $e) {
            $text = ImageCreator::ERROR_TEXT_GENERIC;
        } catch (\Exception $e){
            $text = ImageCreator::ERROR_TEXT_CLIENT_EXCEPTION;
        }

        $image = $imageCreator->createDownloadsImage($text);

        return $this->streamImage($status, $image, $outputFilename);
    }

    /**
     * @Route("/{repository}/version.png",
     *     name="pugx_badge_version",
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     * @Route("/{repository}/v/{latest}.png",
     *     name         = "pugx_badge_version_latest",
     *     defaults     = {"latest" = "stable"},
     *     requirements = {
     *         "type"       = "stable|unstable",
     *         "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *         }
     *     )
     * @Method({"GET"})
     *
     * @param string $repository
     * @param string $latest
     *
     * @return StreamedResponse
     */
    public function versionAction($repository, $latest = 'stable')
    {
        $image = null;
        $outputFilename = sprintf('%s.png', $latest);
        $error = 'Err';
        $status = 500;

        try {
            $package = $this->container->get('package_manager')->fetchPackage($repository);
            $package = $this->container->get('package_manager')->calculateLatestVersions($package);

            if ('stable' == $latest && $package->hasStableVersion()) {
                $image = $this->container->get('image_creator')->createStableImage($package->getLatestStableVersion());
            } else if ($package->hasUnstableVersion()) {
                $image = $this->container->get('image_creator')->createUnstableImage($package->getLatestUnstableVersion());
            }

            $status = 200;
        } catch (\Exception $e) {
            $error = 'Err 01';
        }

        if (null == $image) {
            $image = $this->container->get('image_creator')->createStableImage($error);
        }

        return $this->streamImage($status, $image, $outputFilename);
    }

    /**
     * @param resource $image
     * @param string $outputFilename
     *
     * @return StreamedResponse
     */
    protected function streamImage($status, $image, $outputFilename)
    {
        $imageCreator = $this->container->get('image_creator');

        //generating the streamed response
        $response = new StreamedResponse(null, $status);
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
