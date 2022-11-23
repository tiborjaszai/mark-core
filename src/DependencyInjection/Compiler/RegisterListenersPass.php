<?php

declare(strict_types=1);

namespace JTG\Mark\DependencyInjection\Compiler;

use Exception;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegisterListenersPass implements CompilerPassInterface
{
    /**
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $eventDispatcherDefinition = $container->findDefinition(id: EventDispatcher::class);

        foreach ($container->findTaggedServiceIds(name: 'mark.event_subscriber') as $id => $tags) {
            $subscriberDefinition = $container->findDefinition(id: $id);
            $subscriberRefClass = new ReflectionClass(objectOrClass: $subscriberDefinition->getClass());

            if (true === $subscriberRefClass->implementsInterface(interface: EventSubscriberInterface::class)) {
                $eventDispatcherDefinition->addMethodCall(
                    method: 'addSubscriber',
                    arguments: [new Reference(id: $id)]
                );
            }
        }
    }
}