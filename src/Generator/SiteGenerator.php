<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Renderer\HTML\HTMLRenderer;
use JTG\Mark\Renderer\Markdown\MarkdownRenderer;
use JTG\Mark\Repository\FileRepository;
use Symfony\Component\Filesystem\Filesystem;

class SiteGenerator implements GeneratorInterface
{
    public function __construct(private readonly MarkdownRenderer $markdownRenderer,
                                private readonly HTMLRenderer     $HTMLRenderer,
                                private readonly FileRepository   $fileRepository,
                                private readonly ContextProvider  $contextProvider)
    {
    }

    public function generate(): bool
    {
        $context = $this->contextProvider->context;
        $appConfig = $context->appConfig;

        (new Filesystem())->remove($appConfig->getOutputDir());

        foreach ($appConfig->getCollectionTypes() as $collectionType) {
            if (false === $collectionType->output) {
                continue;
            }

            $markdownFiles = $this->fileRepository
                ->setDirectory(directory: $appConfig->getCollectionsDir() . '/' . $collectionType->slug)
                ->findAll();

            $markdownModels = $this->markdownRenderer->renderFiles(fileInfos: $markdownFiles);

            foreach ($markdownModels as $markdownModel) {
                $this->HTMLRenderer->render(collectionType: $collectionType, file: $markdownModel);
            }
        }

        return true;
    }
}