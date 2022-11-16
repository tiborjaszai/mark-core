<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\HTML;

use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Model\Context\CollectionType;
use JTG\Mark\Model\Markdown\File;
use JTG\Mark\Renderer\Twig\TwigRenderer;
use Symfony\Component\Filesystem\Filesystem;

class HTMLRenderer
{
    private Filesystem $filesystem;
    private Context $context;

    public function __construct(private readonly TwigRenderer $twigRenderer,
                                ContextProvider               $contextProvider)
    {
        $this->filesystem = new Filesystem();
        $this->context = $contextProvider->context;
    }

    public function render(CollectionType $collectionType, File $file): void
    {
        $htmlContent = $this->twigRenderer->render(
            name: $this->getTemplate(collectionType: $collectionType, file: $file),
            context: [
                'node' => array_merge(
                    $file->getFrontMatter(),
                    ['content' => $file->getContent()]
                )
            ]
        );

        if (false === $this->filesystem->exists(files: $this->context->appConfig->getOutputDir())) {
            $this->filesystem->mkdir(dirs: $this->context->appConfig->getOutputDir(), mode: 0755);
        }

        $filename = $this->getOutputFilePath(collectionType: $collectionType, file: $file);
        $this->filesystem->dumpFile(filename: $filename, content: $htmlContent);
    }

    private function getTemplate(CollectionType $collectionType, File $file): string
    {
        $template = sprintf('%s.html.twig', $file->getFrontMatter()['template'] ?? $collectionType->template);
        $templatePath = $this->context->appConfig->getTemplatesDir() . '/' . $template;

        if (false === $this->filesystem->exists(files: $templatePath)) {
            return 'default_template.html.twig';
        }

        return $template;
    }

    private function getOutputFilePath(CollectionType $collectionType, File $file): string
    {
        $appConfig = $this->context->appConfig;

        $dir = $appConfig->getCollectionsDir() . '/' . $collectionType->slug;
        $relativePath = str_replace(search: $dir, replace: '', subject: $file->getRealPath());
        $newFilename = str_replace(search: $file->getExtension(), replace: 'html', subject: $relativePath);

        return $appConfig->getOutputDir() . '/' . $collectionType->slug . '/' . $newFilename;
    }
}