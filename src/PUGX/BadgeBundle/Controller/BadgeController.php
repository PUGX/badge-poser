<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PUGX\BadgeBundle\Controller;

use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use \UnexpectedValueException;

/**
 * Class BadgeController.
 * Main controller for badges.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 */
class BadgeController extends ContainerAware
{
    CONST ERROR_TEXT_GENERIC = 'repository';
    CONST ERROR_TEXT_CLIENT_EXCEPTION = 'connection';
    CONST ERROR_TEXT_CLIENT_BAD_RESPONSE = 'not found?';

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
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return StreamedResponse
     */
    public function downloadsAction($repository, $type = 'total')
    {
        $imageCreator = $this->container->get('image_creator');
        $status = 500;
        $image = null;

        try {
            $package = $this->container->get('package_manager')->fetchPackage($repository);
            $text = $this->container->get('package_manager')->getPackageDownloads($package, $type);
            $image = $imageCreator->createDownloadsImage($text);
            $status = 200;
        } catch (BadResponseException $e) {
            $text = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } catch (UnexpectedValueException $e) {
            $text = self::ERROR_TEXT_CLIENT_EXCEPTION;
        } catch (\Exception $e) {
            $text = self::ERROR_TEXT_GENERIC;
        }

        if (null == $image) {
            $image = $imageCreator->createErrorImage($text);
            $type = 'error';
        }

        $outputFilename = sprintf('%s.png', $type);
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
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return StreamedResponse
     */
    public function versionAction($repository, $latest = 'stable')
    {
        $image = null;
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
        } catch (BadResponseException $e) {
            $error = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } catch (UnexpectedValueException $e) {
            $error = self::ERROR_TEXT_CLIENT_EXCEPTION;
        } catch (\Exception $e) {
            $error = self::ERROR_TEXT_GENERIC;
        }

        if (null == $image) {
            $image = $this->container->get('image_creator')->createErrorImage($error);
            $latest = 'error';
        }

        $outputFilename = sprintf('%s.png', $latest);
        return $this->streamImage($status, $image, $outputFilename);
    }

    /**
     * @param int      $status
     * @param resource $image
     * @param string   $outputFilename
     *
     * @return StreamedResponse
     */
    protected function streamImage($status, $image, $outputFilename, $maxage = 3600, $smaxage = 3600)
    {
        $imageCreator = $this->container->get('image_creator');

        //generating the streamed response
        $response = new StreamedResponse(null, $status);
        $response->setCallback(function () use ($imageCreator, $image) {
            $imageCreator->streamRawImageData($image);
        });

        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$outputFilename.'"');

        //adding cache-control as standard annotation fails here
        $cacheControl = sprintf('public, maxage=%s, s-maxage=%s', $maxage, $smaxage);
        $response->headers->set('Cache-Control', $cacheControl);

        $response->send();

        $imageCreator->destroyImage($image);

        return $response;
    }
}
