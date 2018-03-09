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
use App\Badge\Model\UseCase\CreateVersionBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VersionController.
 * License action for badges.
 *
 * @author Giulio De Donato <liuggio@gmail.com>
 * @author Leonardo Proietti <leonardo.proietti@gmail.com>
 * @author Simone Fumagalli <simone@iliveinperego.com>
 * @author Andrea Giuliano <giulianoand@gmail.com>
 * @author Andrea Giannantonio <a.giannantonio@gmail.com>
 */
class VersionController extends Controller
{
    /**
     * Version action.
     *
     * @param Request $request
     * @param Poser $poser
     * @param ImageFactory $imageFactory
     * @param CreateVersionBadge $createVersionBadge
     * @param string $repository repository
     * @param string $latest latest
     * @param string $format
     * @return Response
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     * @throws \InvalidArgumentException
     */
    public function version(
        Request $request,
        Poser $poser,
        ImageFactory $imageFactory,
        CreateVersionBadge $createVersionBadge,
        $repository,
        $latest,
        $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validFormats(), true)) {
            $format = $request->query->get('format');
        }

        $function = 'create' . ucfirst($latest) . 'Badge';

        $badge = $createVersionBadge->{$function}($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
