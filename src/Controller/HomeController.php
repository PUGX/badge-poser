<?php

namespace App\Controller;

use App\Contributors\Model\Contributor;
use App\Contributors\Service\Repository as ContributorsRepository;
use App\Service\SnippetGeneratorInterface;
use App\Stats\Reader\ReaderInterface;
use PUGX\Poser\Poser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class HomeController extends AbstractController
{
    public function index(
        string $repository,
        ContributorsRepository $contributorsRepository,
        ReaderInterface $redisReader,
        Poser $poser,
        SnippetGeneratorInterface $generator
    ): Response {
        $prefix = \sprintf('More than %s', \number_format($redisReader->totalAccess()));
        $text = 'badges served!!!';
        $formats = \array_diff($poser->validStyles(), ['svg']);

        /** @var Contributor[] $contributors */
        $contributors = $contributorsRepository->all();

        $response = $this->render(
            'home/index.html.twig',
            [
                'repository' => $repository,
                'badges' => $generator->generateAllSnippets($poser, $repository),
                'badges_served_svg' => $poser->generate($prefix, $text, 'CC0066', 'flat'),
                'formats' => $formats,
                'contributors' => $contributors,
            ]
        );

        $response->setMaxAge(6 * 60 * 60);
        $response->setSharedMaxAge(6 * 60 * 60);

        return $response;
    }
}
