<?php

namespace App\Controller\Badge;

use App\Badge\Model\UseCase\CreateDependentsBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DependentsController.
 */
class DependentsController extends AbstractBadgeController
{
    /**
     * Dependents action.
     *
     * @param string $repository repository
     * @param string $format
     */
    public function dependents(
        Request $request,
        Poser $poser,
        CreateDependentsBadge $createDependentsBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validStyles(), true)) {
            $format = $request->query->get('format');
        }

        return $this->serveBadge(
            $imageFactory,
            $createDependentsBadge->createDependentsBadge($repository, $format)
        );
    }
}
