<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use JetBrains\PhpStorm\NoReturn;
use JMS\Serializer\SerializerBuilder;
use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

trait ContextTrait
{
    #[NoReturn] private function configureContextProvider(): void
    {
        $context = $this->buildContext();

        $definition = $this->container->getDefinition(ContextProvider::class);
        $definition = $definition->setArgument('$context', $context);

        $this->container->setDefinition(ContextProvider::class, $definition);
    }

    protected function buildContext(): Context
    {
        // TODO: load collections and collection items, build relations etc.

        return $this->preBuildContext();
    }

    protected function preBuildContext(): Context
    {
        return (SerializerBuilder::create()->build())->deserialize(
            data: json_encode($this->loadConfigs(), JSON_THROW_ON_ERROR),
            type: Context::class,
            format: 'json'
        );
    }

    protected function loadConfigs(): array
    {
        return [
            'mark_config' => [
                'root_dir' => $this->getMarkRootDir()
            ],
            'app_config' => array_merge(
                $this->loadAppConfig(),
                ['root_dir' => $this->getAppRootDir()]
            )
        ];
    }

    protected function loadAppConfig(): array
    {
        $configFilePath = $this->getAppRootDir() . '/config.yaml';

        if (true === file_exists($configFilePath)) {
            try {
                return Yaml::parseFile($configFilePath);
            } catch (ParseException $exception) {
                // ...
            }
        }

        return [];
    }
}