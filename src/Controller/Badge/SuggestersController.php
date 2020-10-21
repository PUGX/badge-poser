<?php

namespace App\Controller\Badge;

use App\Badge\Model\UseCase\CreateSuggestersBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SuggestersController.
 */
class SuggestersController extends AbstractBadgeController
{
    /**
     * Suggesters action.
     *
     * @param string $repository repository
     * @param string $format
     */
    public function suggesters(
        Request $request,
        Poser $poser,
        CreateSuggestersBadge $createSuggestersBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validStyles(), true)) {
            $format = $request->query->get('format');
        }

        return $this->serveBadge(
            $imageFactory,
            $createSuggestersBadge->createSuggestersBadge($repository, $format)
        );
    }
}
