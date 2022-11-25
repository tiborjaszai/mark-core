<?php

declare(strict_types=1);

namespace JTG\Mark\Routing;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Dto\Context\Config\Collection;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class UrlGenerator
{
    private ?BaseUrlGenerator $generator;

    public function __construct(ContextProvider $contextProvider)
    {
        $this->initializeGenerator(contextProvider: $contextProvider);
    }

    protected function initializeGenerator(ContextProvider $contextProvider): void
    {
        $appConfig = $contextProvider->getContext()->appConfig;
        $routes = new RouteCollection();

        foreach ($appConfig->getCollections() as $collectionConfig) {
            $this->initializeRoutes(routes: $routes, collectionConfig: $collectionConfig);
        }

        $this->generator = new BaseUrlGenerator(
            routes: $routes,
            context: new RequestContext(
                baseUrl: $appConfig->site->baseUrl,
                host: $appConfig->site->host,
                scheme: $appConfig->site->scheme,
                httpPort: $appConfig->site->port
            )
        );
    }

    protected function initializeRoutes(RouteCollection $routes, Collection $collectionConfig): void
    {
        if (false === $collectionConfig->output) {
            return;
        }

        $routes->add(name: 'home_index', route: new Route(path: '/'));
        $routes->add(
            name: 'node_show',
            route: new Route(
                path: '/{id}',
                defaults: [],
                requirements: ['id' => '[a-zA-Z0-9-_\/.]+']
            )
        );
    }

    public function generate(string $name, array $parameters = []): ?string
    {
        try {
            return $this->generator->generate(
                name: $name,
                parameters: $parameters,
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            );
        } catch (RouteNotFoundException $notFoundException) {

        }

        return null;
    }
}