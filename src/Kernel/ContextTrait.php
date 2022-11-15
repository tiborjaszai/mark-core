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
        $serializer = SerializerBuilder::create()->build();

        $contextArray = [
            Context::CONFIG_KERNEL_ROOT_DIR_ALIAS => $this->getKernelRootDir(),
            Context::CONFIG_VENDOR_TEMPLATES_ALIAS => $this->getKernelRootDir() . '/dist/templates',
            Context::CONFIG_PROJECT_ROOT_DIR_ALIAS => $this->getProjectDir()
        ];

        if ($config = $this->loadConfig()) {
            $contextArray = array_merge($contextArray, $config);
        }

        return $serializer->deserialize(
            data: json_encode($contextArray, JSON_THROW_ON_ERROR),
            type: Context::class,
            format: 'json'
        );
    }

    protected function loadConfig(): ?array
    {
        $configFilePath = $this->getProjectDir() . '/config.yaml';

        if (true === file_exists($configFilePath)) {
            try {
                $config = Yaml::parseFile($configFilePath);

                foreach (Context::IGNORED_LOCAL_CONFIGS as $ignoredLocalConfig) {
                    if (true === isset($config[$ignoredLocalConfig])) {
                        unset($config[$ignoredLocalConfig]);
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