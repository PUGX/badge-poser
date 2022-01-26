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

use App\Badge\Model\Badge;
use App\Badge\Model\UseCase\CreateLicenseBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * License action for badges.
 */
final class LicenseController extends AbstractBadgeController
{
    /**
     * @throws \InvalidArgumentException
     */
    public function license(
        Request $request,
        Poser $poser,
        CreateLicenseBadge $createLicenseBadge,
        ImageFactory $imageFactory,
        string $repository,
        string $format = Badge::DEFAULT_FORMAT,
        string $style = Badge::DEFAULT_STYLE,
    ): Response {
        $style = $this->checkStyle($request, $poser, $style);

        return $this->serveBadge(
            $imageFactory,
            $createLicenseBadge->createLicenseBadge($repository, $format, $style),
        );
    }
}
