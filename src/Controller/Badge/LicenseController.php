<?php

/*
 * This file is part of the badge-poser package.
 *
 * (c) PUGX <http://pugx.github.io/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Badge;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateLicenseBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LicenseController
 * License action for badges.
 */
class LicenseController extends AbstractController
{
    /**
     * License action.
     *
     * @param Request            $request
     * @param Poser              $poser
     * @param CreateLicenseBadge $createLicenseBadge
     * @param ImageFactory       $imageFactory
     * @param string             $repository         repository
     * @param string             $format
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function license(
        Request $request,
        Poser $poser,
        CreateLicenseBadge $createLicenseBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validFormats(), true)) {
            $format = $request->query->get('format');
        }

        $badge = $createLicenseBadge->createLicenseBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
