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

use App\Badge\Model\UseCase\CreateLicenseBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Badge;
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
        string $format = 'svg',
        string $style = 'flat',
    ): Response {
        if (!\in_array($request->query->get('style'), $poser->validStyles(), true)) {
            $style = Badge::DEFAULT_STYLE;
        }

        return $this->serveBadge(
            $imageFactory,
            $createLicenseBadge->createLicenseBadge($repository, $format, $style),
        );
    }
}
