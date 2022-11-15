<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\HTML;

use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Model\Markdown\MDFile;
use JTG\Mark\Model\Site\Collection;
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

    public function render(Collection $collection, MDFile $file): void
    {
        $htmlContent = $this->twigRenderer->render(
            name: $this->getTemplate(collection: $collection, file: $file),
            context: [
                'node' => $file->getFrontMatter(),
                'content' => $file->getContent()
            ]
        );

        if (false === $this->filesystem->exists(files: $this->context->getOutputDir())) {
            $this->filesystem->mkdir(dirs: $this->context->getOutputDir(), mode: 0755);
        }

        $filename = $this->getOutputFilePath(collection: $collection, file: $file);
        $this->filesystem->dumpFile(filename: $filename, content: $htmlContent);
    }

    private function getTemplate(Collection $collection, MDFile $file): string
    {
        $template = sprintf('%s.html.twig', $file->getFrontMatter()['template'] ?? $collection->template);
        $templatePath = $this->context->getTemplatesDir() . '/' . $template;

        if (false === $this->filesystem->exists(files: $templatePath)) {
            return 'default_template.html.twig';
        }

        return $template;
    }

    private function getOutputFilePath(Collection $collection, MDFile $file): string
    {
        $dir = $this->context->getCollectionsDir() . '/' . $collection->slug;
        $relativePath = str_replace(search: $dir, replace: '', subject: $file->getRealPath());
        $newFilename = str_replace(search: $file->getExtension(), replace: 'html', subject: $relativePath);

        return $this->context->getOutputDir() . '/' . $collection->slug . '/' . $newFilename;
    }
}