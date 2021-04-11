<?php

namespace App\Service;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

final class SnippetGenerator implements SnippetGeneratorInterface
{
    private const PACKAGIST_ROUTE = 'pugx_badge_packagist';

    private RouteCollection $routes;

    public function __construct(
        private RouterInterface $router,
        /* @var array<int, string> */
        private array $allInBadges,
        /* @var array<int, array> */
        private array $badges,
        private string $packagistRoute = self::PACKAGIST_ROUTE
    ) {
        $this->routes = $router->getRouteCollection();
    }

    /**
     * @throws \Exception
     */
    public function generateAllSnippets(string $repository): array
    {
        $repoLink = $this->generateRepositoryLink($repository);

        $snippets = [];
        $snippets['all']['markdown'] = '';

        foreach ($this->badges as $badge) {
            $img = $this->generateImg($badge, $repository);
            $markdown = $this->generateMarkdown($badge, $img, $repoLink);
            $snippets['badges'][] = [
                'name' => $badge['name'],
                'label' => $badge['label'],
                'markdown' => $markdown,
                'img' => $img,
                'imgLink' => $repoLink,
                'featured' => \in_array($badge['name'], $this->allInBadges, true),
            ];

            if (\in_array($badge['name'], $this->allInBadges, true)) {
                $snippets['all']['markdown'] .= ' '.$markdown;
            }
        }

        $snippets['all']['markdown'] = \trim($snippets['all']['markdown']);

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
