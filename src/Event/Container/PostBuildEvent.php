<?php

declare(strict_types=1);

namespace JTG\Mark\Event\Container;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class PostBuildEvent
{
    public const NAME = 'container.event.post_build';

    public function __construct(private readonly ContainerBuilder $container)
    {
    }

    public function getContainer(): ContainerBuilder
    {
        return $this->container;
    }
}