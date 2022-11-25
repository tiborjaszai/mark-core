<?php

declare(strict_types=1);

namespace JTG\Mark\DependencyInjection\Compiler;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Dto\Site\Collection;
use JTG\Mark\Dto\Site\File;
use JTG\Mark\Renderer\Markdown\MarkdownRenderer;
use JTG\Mark\Repository\FileRepository;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigureContextPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(id: ContextProvider::class);

        $context = $definition->getArgument(index: '$context');
        $definition->setArgument(key: '$context', value: $this->configureContext(context: $context));
    }

    protected function configureContext(Context $context): Context
    {
        $files = (new FileRepository(context: $context))->findAll();

        $this->prepareCollections(context: $context);
        $this->addFilesToCollections(context: $context, files: $files);

        return $context;
    }

    protected function prepareCollections(Context $context): void
    {
        foreach ($context->appConfig->getCollections() as $collectionConfig) {
            $collection = Collection::fromCollectionConfig(collectionConfig: $collectionConfig);
            $context->addCollection(collection:  $collection);
        }
    }

    /**
     * @param array<SplFileInfo> $files
     */
    protected function addFilesToCollections(Context $context, array $files): void
    {
        $markdownRenderer = new MarkdownRenderer();

        foreach ($files as $file) {
            $fileModel = (File::fromFileInfo(context: $context, fileInfo: $file));
            $fileModel = $markdownRenderer->renderFile(context: $context, file: $fileModel);

            switch (true) {
                case null !== ($collection = $context->getCollection(collectionName: $fileModel->getCollectionName() ?? 'root')):
                    $collection->addItem(item: $fileModel);
                    break;

                default:
                    $this->configureContextDataProp(context: $context, file: $fileModel);
            }
        }
    }

    protected function configureContextDataProp(Context $context, File $file): void
    {
        if ($file->getRelativePath() !== $context->appConfig->dataDir ||
            false === in_array($file->getExtension(), $context->markConfig->yamlExtensions, true)) {
            return;
        }

        try {
            $data = Yaml::parseFile(filename: $file->getAbsolutePath()) ?? [];

            if (false === empty($data)) {
                $context->addData(key: $file->getFilenameWithoutExtension(), data: $data);
            }
        } catch (ParseException $exception) {
            // ...
        }
    }
}