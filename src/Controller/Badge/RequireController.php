<?php

namespace App\Controller\Badge;

use App\Badge\Model\Badge;
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
        string $format = Badge::DEFAULT_FORMAT,
        string $style = Badge::DEFAULT_STYLE,
    ): Response {
        $style = $this->checkStyle($request, $poser, $style);

        return $this->serveBadge(
            $imageFactory,
            $createRequireBadge->createRequireBadge($repository, $type, $format, $style),
        );
    }
}
