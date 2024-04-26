<?php

namespace App\Controller;

use App\Contributors\Service\Repository as ContributorsRepository;
use App\Service\SnippetGeneratorInterface;
use App\Stats\Reader\ReaderInterface;
use PUGX\Poser\Poser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home', defaults: ['repository' => 'phpunit/phpunit'])]
    #[Route('/show/{repository}', name: 'show', requirements: ['repository' => '[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+?'])]
    public function index(
        string $repository,
        ContributorsRepository $contributorsRepository,
        ReaderInterface $redisReader,
        Poser $poser,
        SnippetGeneratorInterface $generator
    ): Response {
        $prefix = \sprintf('More than %s', \number_format($redisReader->totalAccess()));
        $text = 'badges served!!!';

        $contributors = $contributorsRepository->all();

        $response = $this->render(
            'home/index.html.twig',
            [
                'repository' => $repository,
                'badges' => $generator->generateAllSnippets($poser, $repository),
                'badges_served_svg' => $poser->generate($prefix, $text, 'CC0066', 'flat'),
                'contributors' => $contributors,
            ]
        );

        $response->setMaxAge(6 * 60 * 60);
        $response->setSharedMaxAge(6 * 60 * 60);

        return $response;
    }
}
