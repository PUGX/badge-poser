<?php

namespace App\Service;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class SnippetGenerator.
 */
class SnippetGenerator implements SnippetGeneratorInterface
{
    private RouterInterface $router;

    private array $badges;

    private array $allInBadges;

    private ?RouteCollection $routes;

    private string $packagistRoute;

    public function __construct(RouterInterface $router, array $allInBadges, array $badges, string $packagist_route = 'pugx_badge_packagist')
    {
        $this->router = $router;
        $this->routes = $router->getRouteCollection();
        $this->allInBadges = $allInBadges;
        $this->badges = $badges;
        $this->packagistRoute = $packagist_route;
    }

    /**
     * @throws \Exception
     */
    public function generateAllSnippets(string $repository): array
    {
        $snippets = [];
        $snippets['all']['markdown'] = '';

        foreach ($this->badges as $badge) {
            $markdown = $this->generateMarkdown($badge, $repository);
            $snippets['badges'][] = [
                'name' => $badge['name'],
                'label' => $badge['label'],
                'markdown' => $markdown,
                'img' => $this->generateImg($badge, $repository),
                'featured' => \in_array($badge['name'], $this->allInBadges, true),
            ];

            if (\in_array($badge['name'], $this->allInBadges, true)) {
                $snippets['all']['markdown'] .= ' '.$markdown;
            }
        }

        $snippets['all']['markdown'] = trim($snippets['all']['markdown']);

        return $snippets;
    }

    /**
     * @throws \Exception
     */
    public function generateMarkdown(array $badge, string $repository): string
    {
        return sprintf(
            '[![%s](%s)](%s)',
            $badge['label'],
            $this->generateImg($badge, $repository),
            $this->generateRepositoryLink($repository)
        );
    }

    /**
     * @throws \Exception
     */
    public function generateImg(array $badge, string $repository): string
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
    public function generateRepositoryLink(string $repository): string
    {
        return $this->router->generate($this->packagistRoute, ['repository' => $repository]);
    }

    /**
     * @throws \RuntimeException
     */
    private function compileRouteParametersForBadge(array $badge): array
    {
        $parameters = [];
        $route = $this->routes->get($badge['route']);

        if (!$route) {
            throw new \RuntimeException(sprintf('The route "%s" was not found', $badge['route']));
        }

        $routeParameters = array_keys(array_merge($route->getDefaults(), $route->getRequirements()));

        foreach ($routeParameters as $routeParameter) {
            if (\array_key_exists($routeParameter, $badge)) {
                $parameters[$routeParameter] = $badge[$routeParameter];
            }
        }

        return $parameters;
    }
}
