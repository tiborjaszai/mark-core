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
    public function getDefaultContextArray(): array
    {
        return [
            'kernel_root_dir' => $this->getKernelRootDir(),
            'project_root_dir' => $this->getProjectDir(),
            'dist_dir' => $this->getProjectDir() . '/dist',
            'collections_dir' => $this->getProjectDir() . '/dist/collections',
            'data_dir' => $this->getProjectDir() . '/dist/data',
            'includes_dir' => $this->getProjectDir() . '/dist/includes',
            'templates_dir' => $this->getProjectDir() . '/dist/templates',
            'output_dir' => $this->getProjectDir() . '/build'
        ];
    }

    protected function getIgnoredConfigs(): array
    {
        return [
            'kernel_root_dir',
            'project_root_dir'
        ];
    }

    #[NoReturn] private function configureContextProvider(): void
    {
        $serializer = SerializerBuilder::create()->build();
        $contextArray = $this->getDefaultContextArray();

        if ($config = $this->loadConfig()) {
            $contextArray = array_merge($contextArray, $config);
        }

        $context = $serializer->deserialize(
            data: json_encode($contextArray, JSON_THROW_ON_ERROR),
            type: Context::class,
            format: 'json'
        );

        $definition = $this->container->getDefinition(ContextProvider::class);
        $definition = $definition->setArgument('$context', $context);

        $this->container->setDefinition(ContextProvider::class, $definition);
    }

    protected function loadConfig(): ?array
    {
        $configFilePath = $this->getProjectDir() . '/config.yaml';

        if (true === file_exists($configFilePath)) {
            try {
                $config = Yaml::parseFile($configFilePath);

                foreach ($this->getIgnoredConfigs() as $ignoredConfig) {
                    if (true === isset($config[$ignoredConfig])) {
                        unset($config[$ignoredConfig]);
                    }
                }

                return $config;
            } catch (ParseException $exception) {
                // ...
            }
        }

        return null;
    }
}