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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use \UnexpectedValueException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BadgeController.
 * Main controller for badges.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 */
class BadgeController extends Controller
{
    CONST TEXT_NO_STABLE_RELEASE = 'No release';
    CONST TEXT_NO_LICENSE = 'No';
    CONST ERROR_TEXT_GENERIC = 'repository';
    CONST ERROR_TEXT_CLIENT_EXCEPTION = 'connection';
    CONST ERROR_TEXT_CLIENT_BAD_RESPONSE = 'not found?';

    /**
     * @Route("/search_packagist", name="search_packagist")
     * @Method("GET")
     */
    public function searchPackagistAction(Request $request)
    {
        $responseContent = array();
        $packageName = $request->query->get('name');

        $packagistResponse = $this->container->get('packagist_client')->search($packageName);

        foreach ($packagistResponse as $package) {
            $responseContent[] = array("id" => $package->getName(), "description" => $package->getDescription());
        }

        $response = new JsonResponse($responseContent);

        return $response;
    }

    /**
     * Downloads action.
     *
     * @param string $repository repository
     * @param string $type       badge type
     *
     * @Route("/{repository}/downloads.svg",
     *     name         = "pugx_badge_download",
     *     requirements = {
     *          "repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"
     *       }
     *     )
     * @Route("/{repository}/d/{type}.svg",
     *     name         = "pugx_badge_download_type",
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

        $when = '';
        if ('daily' === $type) {
            $when = 'today';
        } elseif ('monthly' === $type) {
            $when = 'this month';
        }

        try {
            $package = $this->container->get('package_manager')->fetchPackage($repository);
            $text = $this->container->get('package_manager')->getPackageDownloads($package, $type);
            $text = $this->container->get('normalizer')->normalize($text);
            $image = $imageCreator->createDownloadsImage(sprintf("%s %s", $text, $when));
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

        $outputFilename = sprintf('%s.svg', $type);

        return $this->createSVGResponse($status, (string) $image, $outputFilename);
    }

    /**
     * Version action.
     *
     * @param string $repository repository
     * @param string $latest     latest
     *
     * @Route("/{repository}/version.svg",
     *     name="pugx_badge_version",
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     * @Route("/{repository}/v/{latest}.svg",
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
            } elseif ('stable' == $latest) {
                $image = $this->container->get('image_creator')->createStableNoImage(self::TEXT_NO_STABLE_RELEASE);
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

        if (null === $image) {
            $image = $this->container->get('image_creator')->createErrorImage($error);
            $latest = 'error';
        }

        $outputFilename = sprintf('%s.svg', $latest);

        return $this->createSVGResponse($status, (string) $image, $outputFilename);
    }

    /**
     * License action.
     *
     * @param string $repository repository
     *
     * @Route("/{repository}/license.svg",
     *     name="pugx_badge_license",
     *     requirements={"repository" = "[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?"}
     *     )
     *
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     *
     * @return StreamedResponse
     */
    public function licenseAction($repository)
    {
        $image = null;
        $error = self::ERROR_TEXT_GENERIC;
        $status = 500;

        try {
            $package = $this->container->get('package_manager')->fetchPackage($repository);
            $package = $this->container->get('package_manager')->calculateLatestVersions($package);
            $license = $package->getLicense();

            if (empty($license)) {
                $image = $this->container->get('image_creator')->createLicenseImage(self::TEXT_NO_LICENSE);
            } else {
                $image = $this->container->get('image_creator')->createLicenseImage($license);
            }
            $status = 200;
        } catch (BadResponseException $e) {
            $error = self::ERROR_TEXT_CLIENT_BAD_RESPONSE;
        } catch (UnexpectedValueException $e) {
            $error = self::ERROR_TEXT_CLIENT_EXCEPTION;
        } catch (\Exception $e) {
            $error = self::ERROR_TEXT_GENERIC;
        }

        if (null === $image) {
            $image = $this->container->get('image_creator')->createErrorImage($error);
        }

        $outputFilename = sprintf('%s.svg', 'version');

        return $this->createSVGResponse($status, (string) $image, $outputFilename);
    }

    /**
     * @param int    $status
     * @param string $image
     * @param string $outputFilename
     * @param int    $maxage
     * @param int    $smaxage
     *
     * @return Response
     */
    private function createSVGResponse($status, $image, $outputFilename, $maxage = 3600, $smaxage = 3600)
    {
        $response = new Response($image, $status);

        $response->headers->set('Content-Type', 'image/svg+xml;charset=utf-8');
        $response->headers->set('Content-Disposition', 'inline; filename="'.$outputFilename.'"');

        //adding cache-control as standard annotation fails here
        $cacheControl = sprintf('public, maxage=%s, s-maxage=%s', $maxage, $smaxage);
        $response->headers->set('Cache-Control', $cacheControl);

        return $response;
    }
}
