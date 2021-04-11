<?php

namespace App\Controller\Badge;

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
        string $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validStyles(), true)) {
            $format = (string) $request->query->get('format');
        }

        return $this->serveBadge(
            $imageFactory,
            $createSuggestersBadge->createSuggestersBadge($repository, $format)
        );
    }
}
