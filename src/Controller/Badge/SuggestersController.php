<?php

namespace App\Controller\Badge;

use App\Badge\Model\Badge;
use App\Badge\Model\UseCase\CreateSuggestersBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SuggestersController extends AbstractBadgeController
{
    public function suggesters(
        Request $request,
        Poser $poser,
        CreateSuggestersBadge $createSuggestersBadge,
        ImageFactory $imageFactory,
        string $repository,
        string $format = Badge::DEFAULT_FORMAT,
        string $style = Badge::DEFAULT_STYLE,
    ): Response {
        $style = $this->checkStyle($request, $poser, $style);

        return $this->serveBadge(
            $imageFactory,
            $createSuggestersBadge->createSuggestersBadge($repository, $format, $style),
        );
    }
}
