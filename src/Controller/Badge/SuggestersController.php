<?php

namespace App\Controller\Badge;

use App\Badge\Infrastructure\ResponseFactory;
use App\Badge\Model\UseCase\CreateSuggestersBadge;
use App\Badge\Service\ImageFactory;
use PUGX\Poser\Poser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
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
     * @param Request               $request
     * @param Poser                 $poser
     * @param CreateSuggestersBadge $createSuggestersBadge
     * @param ImageFactory          $imageFactory
     * @param string                $repository            repository
     * @param string                $format
     *
     * @return Response
     * @Cache(maxage="3600", smaxage="3600", public=true)
     */
    public function suggesters(
        Request $request,
        Poser $poser,
        CreateSuggestersBadge $createSuggestersBadge,
        ImageFactory $imageFactory,
        $repository,
        $format = 'svg'
    ): Response {
        if (\in_array($request->query->get('format'), $poser->validFormats(), true)) {
            $format = $request->query->get('format');
        }

        $badge = $createSuggestersBadge->createSuggestersBadge($repository, $format);
        $image = $imageFactory->createFromBadge($badge);

        return ResponseFactory::createFromImage($image, 200);
    }
}
