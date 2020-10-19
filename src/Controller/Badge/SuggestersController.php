<?php

namespace App\Controller\Badge;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateSuggestersBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SuggestersController.
 */
class SuggestersController extends AbstractController
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

        $badge = $createSuggestersBadge->createSuggestersBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        $maxage = 24 * 60 * 60;
        $smaxage = 24 * 60 * 60;
        return ResponseFactory::createFromImage($image, 200, $maxage, $smaxage);
    }
}
