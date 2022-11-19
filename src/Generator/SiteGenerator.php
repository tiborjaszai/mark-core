<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Renderer\HTML\HTMLRenderer;
use JTG\Mark\Renderer\Markdown\MarkdownRenderer;
use Symfony\Component\Filesystem\Filesystem;

class SiteGenerator
{
    public function __construct(private readonly ContextProvider  $contextProvider,
                                private readonly HTMLRenderer     $HTMLRenderer,
                                private readonly MarkdownRenderer $markdownRenderer)
    {
    }

    public function generate(): bool
    {
        $filesystem = new Filesystem();
        $context = $this->contextProvider->context;

        $filesystem->remove(files: $context->appConfig->getOutputDirPath());
        $filesystem->mkdir(dirs: $context->appConfig->getOutputDirPath(), mode: 0755);

        foreach ($context->getCollections() as $collection) {
            if (!$collection->getOutput() || 0 === $collection->getItemsCount()) {
                continue;
            }

            foreach ($collection->getItems() as $file) {
                switch (true) {
                    case true === in_array($file->getExtension(), MarkdownRenderer::EXTENSIONS, true):
                        if ($renderedFile = $this->markdownRenderer->renderFile(file: $file)) {
                            $this->HTMLRenderer->render(file: $renderedFile);
                        }
                        break;

                    default:
                        $filesystem->copy(
                            originFile: $file->getAbsolutePath(),
                            targetFile: PathGenerator::generateOutputFilePath($context, $file),
                            overwriteNewerFiles: true
                        );
                }
            }
        }

        return true;
    }
}