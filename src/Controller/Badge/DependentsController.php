<?php

namespace App\Controller\Badge;

use App\Badge\Model\UseCase\CreateDependentsBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DependentsController extends AbstractBadgeController
{
    public function dependents(
        Request $request,
        Poser $poser,
        CreateDependentsBadge $createDependentsBadge,
        ImageFactory $imageFactory,
        string $repository,
        string $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validStyles(), true)) {
            $format = (string) $request->query->get('format');
        }

        return $this->serveBadge(
            $imageFactory,
            $createDependentsBadge->createDependentsBadge($repository, $format)
        );
    }
}
