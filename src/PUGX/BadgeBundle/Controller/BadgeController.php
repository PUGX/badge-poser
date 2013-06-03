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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use PUGX\BadgeBundle\Exception\UnexpectedValueException;

/**
 * Class BadgeController.
 * Main controller for badges.
 *
 * @package PUGX\BadgeBundle\Controller
 */
class BadgeController extends ContainerAware
{
    CONST ERROR_TEXT_GENERIC = 'ERR 1 ';
    CONST ERROR_TEXT_NOT_A_NUMBER = 'ERR 2 ';
    CONST ERROR_TEXT_CLIENT_EXCEPTION = 'ERR 3 ';

    /**
     * Downloads action.
     *
     * @param string $repository repository
     * @param string $type       badge type
     *
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
            $text = self::ERROR_TEXT_CLIENT_EXCEPTION;
        } catch (\Exception $e) {
            $text = self::ERROR_TEXT_GENERIC;
        }

        $image = $imageCreator->createDownloadsImage($text);

        return $this->streamImage($status, $image, $outputFilename);
    }

    /**
     * Version action.
     *
     * @param string $repository repository
     * @param string $latest     latest
     *
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
     * @return StreamedResponse
     */
    public function versionAction($repository, $latest = 'stable')
    {
        $image = null;
        $outputFilename = sprintf('%s.png', $latest);
        $error = self::ERROR_TEXT_GENERIC;
        $status = 500;

        try {
            $package = $this->container->get('package_manager')->fetchPackage($repository);
            $package = $this->container->get('package_manager')->calculateLatestVersions($package);

            if ('stable' == $latest && $package->hasStableVersion()) {
                $image = $this->container->get('image_creator')->createStableImage($package->getLatestStableVersion());
            } elseif ($package->hasUnstableVersion()) {
                $image = $this->container->get('image_creator')->createUnstableImage($package->getLatestUnstableVersion());
            }

            $status = 200;
        } catch (UnexpectedValueException $e) {
            $error = self::ERROR_TEXT_CLIENT_EXCEPTION;
        } catch (\Exception $e) {
            $error = self::ERROR_TEXT_GENERIC;
        }

        if (null == $image) {
            $image = $this->container->get('image_creator')->createStableImage($error);
        }

        return $this->streamImage($status, $image, $outputFilename);
    }

    /**
     * @param int      $status
     * @param resource $image
     * @param string   $outputFilename
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
