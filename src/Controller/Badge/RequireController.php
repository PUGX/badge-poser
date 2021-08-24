<?php

namespace App\Controller\Badge;

use App\Badge\Model\UseCase\CreateRequireBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireController extends AbstractBadgeController
{
    public function require(
        Request $request,
        Poser $poser,
        CreateRequireBadge $createRequireBadge,
        ImageFactory $imageFactory,
        string $repository,
        string $type,
        string $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validStyles(), true)) {
            $format = (string) $request->query->get('format');
        }

        return $this->serveBadge(
            $imageFactory,
            $createRequireBadge->createRequireBadge($repository, $type, $format)
        );
    }
}
