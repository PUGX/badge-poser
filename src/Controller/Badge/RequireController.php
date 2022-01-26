<?php

namespace App\Controller\Badge;

use App\Badge\Model\UseCase\CreateRequireBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Badge;
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
        string $format = 'svg',
        string $style = 'flat',
    ): Response {
        $style = $request->query->get('style', $style);
        if (!\in_array($style, $poser->validStyles(), true)) {
            $style = Badge::DEFAULT_STYLE;
        }

        return $this->serveBadge(
            $imageFactory,
            $createRequireBadge->createRequireBadge($repository, $type, $format, $style),
        );
    }
}
