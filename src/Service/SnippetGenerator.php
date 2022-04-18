<?php

namespace App\Service;

use App\Dictionary\AllInBadges;
use App\Dictionary\Badges;
use PUGX\Poser\Poser;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class SnippetGenerator implements SnippetGeneratorInterface
{
    private const PACKAGIST_ROUTE = 'pugx_badge_packagist';

    private RouteCollection $routes;

    public function __construct(
        private RouterInterface $router,
        private string $packagistRoute = self::PACKAGIST_ROUTE
    ) {
        $this->routes = $router->getRouteCollection();
    }

    /**
     * @throws \Exception
     */
    public function generateAllSnippets(Poser $poser, string $repository): array
    {
        $repoLink = $this->generateRepositoryLink($repository);

        $snippets = [];
        $snippets['all']['markdown'] = '';
        $badges = Badges::getAll();

        foreach ($badges as $badge) {
            $img = $this->generateImg($badge, $repository);
            $markdown = $this->generateMarkdown($badge, $img, $repoLink);
            $snippets['badges'][] = [
                'name' => $badge['name'],
                'label' => $badge['label'],
                'markdown' => $markdown,
                'img' => $img,
                'imgLink' => $repoLink,
                'featured' => AllInBadges::isABadgeName($badge['name']),
            ];

            if (AllInBadges::isABadgeName($badge['name'])) {
                $snippets['all']['markdown'] .= ' '.$markdown;
            }
        }

        $snippets['all']['markdown'] = \trim($snippets['all']['markdown']);

        $badge = [
            'name' => 'latest_stable_version',
            'route' => 'pugx_badge_version_latest',
        ];
        $validStyles = $poser->validStyles();
        \sort($validStyles);
        foreach ($validStyles as $style) {
            $badge['style'] = $style;
            $badge['label'] = $style;
            $img = $this->generateImg($badge, $repository);
            $markdown = $this->generateMarkdown($badge, $img, $repoLink);
            $snippets['badge_styles'][] = [
                'name' => $badge['name'],
                'label' => $badge['label'],
                'markdown' => $markdown,
                'img' => $img,
                'imgLink' => $repoLink,
                'featured' => false,
            ];
        }

        return $snippets;
    }

    /**
     * @param array<string, string> $badge
     *
     * @throws \Exception
     */
    private function generateMarkdown(array $badge, string $img, string $repoLink): string
    {
        return \sprintf(
            '[![%s](%s)](%s)',
            $badge['label'],
            $img,
            $repoLink
        );
    }

    /**
     * @param array<string, string> $badge
     *
     * @throws \Exception
     */
    private function generateImg(array $badge, string $repository): string
    {
        $badge['repository'] = $repository;
        $parameters = $this->compileRouteParametersForBadge($badge);

        return $this->router->generate($badge['route'], $parameters, RouterInterface::ABSOLUTE_URL);
    }

    /**
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     */
    private function generateRepositoryLink(string $repository): string
    {
        return $this->router->generate($this->packagistRoute, ['repository' => $repository]);
    }

    /**
     * @param array<string, string> $badge
     *
     * @return array<int|string, string>
     *
     * @throws \RuntimeException
     */
    private function compileRouteParametersForBadge(array $badge): array
    {
        $route = $this->routes->get($badge['route']);
        if (null === $route) {
            throw new \RuntimeException(\sprintf('The route "%s" was not found', $badge['route']));
        }

        $routeParameters = \array_keys(\array_merge($route->getDefaults(), $route->getRequirements()));

        $parameters = [];
        foreach ($routeParameters as $routeParameter) {
            if (\array_key_exists($routeParameter, $badge)) {
                $parameters[$routeParameter] = $badge[$routeParameter];
            }
        }

        return $parameters;
    }
}
