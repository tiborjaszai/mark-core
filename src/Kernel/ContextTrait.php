<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use JMS\Serializer\SerializerBuilder;
use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Model\Context\Collection as CollectionConfig;
use JTG\Mark\Model\Site\Collection;
use JTG\Mark\Model\Site\File as FileModel;
use JTG\Mark\Repository\FileRepository;
use JTG\Mark\Util\ArrayHelper;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

trait ContextTrait
{
    private function configureContextProvider(): void
    {
        $context = $this->buildContext();

        $definition = $this->container->getDefinition(id: ContextProvider::class);
        $definition = $definition->setArgument(key: '$context', value: $context);

        $this->container->setDefinition(id: ContextProvider::class, definition: $definition);
    }

    protected function buildContext(): Context
    {
        $context = $this->preBuildContext();
        $files = (new FileRepository(context: $context))->findAll();

        return $this->postBuildContext(context: $context, files: $files);
    }

    protected function preBuildContext(): Context
    {
        /** @var Context $context */
        $context = (SerializerBuilder::create()->build())->deserialize(
            data: json_encode($this->loadConfigs(), JSON_THROW_ON_ERROR),
            type: Context::class,
            format: 'json'
        );

        $context
            ->appConfig
            ->addCollection(new CollectionConfig(name: 'global', template: 'default_template', output: true));

        return $context;
    }

    /**
     * @param array<SplFileInfo> $files
     */
    protected function postBuildContext(Context $context, array $files): Context
    {
        /** @var array<Collection> $collections */
        $collections = [];

        foreach ($files as $file) {
            $fileModel = (FileModel::fromFileInfo(fileInfo: $file));

            if ($collectionConfig = $context->appConfig->getCollection(name: $fileModel->getCollectionName(), defaultGlobal: true)) {
                $collectionName = $collectionConfig->name;

                if (false === isset($collections[$collectionName])) {
                    $collections[$collectionName] = Collection::fromCollectionConfig(collectionConfig: $collectionConfig);
                }

                $collections[$collectionName]->addItem(item: $fileModel);
            } else {
                // unmanaged files...
            }
        }

        $context->setCollections(collections: $collections);

        return $context;
    }

    protected function loadConfigs(): array
    {
        $defaultConfigs = $this->loadConfigFile($this->getMarkConfigFile());
        $appConfig = $this->loadConfigFile($this->getAppConfigFile());

        return [
            'env' => $this->env,
            'mark_config' => array_merge($defaultConfigs['mark'], ['root_dir' => $this->getMarkRootDir()]),
            'app_config' => array_merge(
                ArrayHelper::recursiveAssocMerge(first: $defaultConfigs['app'], second: $appConfig),
                ['root_dir' => $this->getAppRootDir()]
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