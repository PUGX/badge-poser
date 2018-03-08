<?php

namespace App\Controller\Badge;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateDependentsBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DependentsController.
 */
class DependentsController extends Controller
{
    /**
     * Dependents action.
     *
     * @param Request $request
     * @param Poser $poser
     * @param CreateDependentsBadge $createDependentsBadge
     * @param ImageFactory $imageFactory
     * @param string $repository repository
     * @param string $format
     * @return Response
     * @Method({"GET"})
     * @Cache(maxage="3600", smaxage="3600", public=true)
     */
    public function dependentsAction(
        Request $request,
        Poser $poser,
        CreateDependentsBadge $createDependentsBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ) : Response {
        if (\in_array($request->query->get('format'), $poser->validFormats(), true)) {
            $format = $request->query->get('format');
        }

        $badge = $createDependentsBadge->createDependentsBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
