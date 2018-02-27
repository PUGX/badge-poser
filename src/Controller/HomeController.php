<?php

namespace App\Controller;

use App\Contributors\Service\Repository as ContributorsRepository;
use App\Contributors\Model\Contributor;
use App\Service\ReaderInterface;
use PUGX\Poser\Poser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{

    public function index(
        $repository,
        ContributorsRepository $contributorsRepository,
        ReaderInterface $statsReader,
        Poser $poser
    ): Response
    {
        /** @var Contributor[] $contributors */
        $contributors = $contributorsRepository->all();

        $prefix = sprintf('More than %s', number_format($statsReader->totalAccess()));
        $text = 'badges served !!';
        $formats = array_diff($poser->validFormats(), ["svg"]);

        return $this->render(
            'home/index.html.twig',
            [
                'repository' => $repository,
                'badges_served_svg' => $poser->generate($prefix, $text, 'CC0066', 'flat'),
                'formats' => $formats,
                'contributors' => $contributors
            ]
        );
    }

    public function show($repository): Response
    {
        return $this->render(
            'home/index.html.twig',
            [
                'repository' => $repository,
            ]
        );
    }
}
