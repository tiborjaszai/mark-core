<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Model\Site\Collection;
use JTG\Mark\Renderer\HTML\HTMLRenderer;
use JTG\Mark\Renderer\Markdown\MarkdownRenderer;
use JTG\Mark\Repository\FileRepository;

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

        /** @var Collection $collection */
        foreach ($context->collections as $collection) {
            $collectionDir = $context->collectionsDir . '/' . $collection->slug;

            $markdownFiles = $this->fileRepository
                ->setDirectory(directory: $collectionDir)
                ->findAll();

            $markdownModels = $this->markdownRenderer->renderFiles(fileInfos: $markdownFiles);

            foreach ($markdownModels as $markdownModel) {
                $this->HTMLRenderer->render(MDFile: $markdownModel, collection: $collection, dir: $collectionDir);
            }
        }

        return true;
    }
}