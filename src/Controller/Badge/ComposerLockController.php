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
use App\Badge\Model\UseCase\CreateComposerLockBadge;
use App\Badge\Service\ImageFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

/**
 * Class ComposerLockController
 * Composer-lock action for badges.
 */
class ComposerLockController extends AbstractController
{
    /**
     * ComposerLock action.
     *
     * @param Request                 $request
     * @param ImageFactory            $imageFactory
     * @param CreateComposerLockBadge $composerLockBadge
     * @param string                  $repository        repository
     * @param string                  $format
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function composerLock(
        Request $request,
        ImageFactory $imageFactory,
        CreateComposerLockBadge $composerLockBadge,
        $repository,
        $format = 'svg'
    ): Response {
        if ('plastic' === $request->query->get('format')) {
            $format = 'plastic';
        }

        $badge = $composerLockBadge->createComposerLockBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
