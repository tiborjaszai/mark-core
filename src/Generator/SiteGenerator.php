<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Renderer\HTML\HTMLRenderer;
use JTG\Mark\Renderer\Markdown\MarkdownRenderer;
use Symfony\Component\Filesystem\Filesystem;

class SiteGenerator
{
    public function __construct(private readonly ContextProvider $contextProvider,
                                private readonly HTMLRenderer    $HTMLRenderer)
    {
    }

    public function generate(): bool
    {
        $filesystem = new Filesystem();
        $context = $this->contextProvider->getContext();

        $filesystem->remove(files: $context->appConfig->getOutputDirPath());
        $filesystem->mkdir(dirs: $context->appConfig->getOutputDirPath(), mode: 0755);

        foreach ($context->getCollections() as $collection) {
            if (false === $collection->getOutput() || 0 === $collection->getItemsCount()) {
                continue;
            }

            foreach ($collection->getItems() as $file) {
                switch (true) {
                    case true === in_array($file->getExtension(), MarkdownRenderer::EXTENSIONS, true):
                        $this->HTMLRenderer->render(file: $file);
                        break;

                    default:
                        $filesystem->copy(
                            originFile: $file->getAbsolutePath(),
                            targetFile: $file->getOutputFilepath(),
                            overwriteNewerFiles: true
                        );
                }
            }
        }

        return true;
    }
}