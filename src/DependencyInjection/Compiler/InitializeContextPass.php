<?php

declare(strict_types=1);

namespace JTG\Mark\DependencyInjection\Compiler;

use Exception;
use JMS\Serializer\SerializerBuilder;
use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Dto\Context\Config\Collection as CollectionConfig;
use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Util\ArrayHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class InitializeContextPass implements CompilerPassInterface
{
    private ?ParameterBagInterface $parameterBag = null;

    /**
     * @throws Exception
     */
    public function process(ContainerBuilder $container): void
    {
        $this->parameterBag = $container->getParameterBag();

        $context = $this->initializeContext();

        $definition = $container->findDefinition(id: ContextProvider::class);
        $definition->setArgument(key: '$context', value: $context);
    }

    protected function initializeContext(): Context
    {
        /** @var Context $context */
        $context = (SerializerBuilder::create()->build())->deserialize(
            data: json_encode(value: $this->loadConfigs(), flags: JSON_THROW_ON_ERROR),
            type: Context::class,
            format: 'json'
        );

        $context
            ->appConfig
            ->addCollection(new CollectionConfig(name: 'root', template: 'default_template', output: true));

        return $context;
    }

    protected function loadConfigs(): array
    {
        $defaultConfigs = $this->loadConfigFile($this->parameterBag->get(name: 'mark.config_file_path'));
        $appConfig = $this->loadConfigFile($this->parameterBag->get(name: 'app.config_file_path'));

        return [
            'env' => $this->parameterBag->get(name: 'app.env'),
            'mark_config' => array_merge($defaultConfigs['mark'], ['root_dir' => $this->parameterBag->get(name: 'mark.root_dir')]),
            'app_config' => array_merge(
                ArrayHelper::recursiveAssocMerge(first: $defaultConfigs['app'], second: $appConfig),
                ['root_dir' => $this->parameterBag->get(name: 'app.root_dir')]
            )
        ];
    }

    protected function loadConfigFile(string $path): array
    {
        if (true === file_exists(filename: $path)) {
            try {
                return Yaml::parseFile(filename: $path) ?? [];
            } catch (ParseException $exception) {
                // ...
            }
        }

        return [];
    }
}