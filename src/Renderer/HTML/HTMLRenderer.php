<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\HTML;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Model\Markdown\MDFile;
use JTG\Mark\Model\Site\Collection;
use JTG\Mark\Renderer\Twig\TwigRenderer;
use Symfony\Component\Filesystem\Filesystem;

class HTMLRenderer
{
    private Filesystem $filesystem;

    public function __construct(private readonly TwigRenderer    $twigRenderer,
                                private readonly ContextProvider $contextProvider)
    {
        $this->filesystem = new Filesystem();
    }

    public function render(MDFile $MDFile, Collection $collection, string $dir): void
    {
        $outputDir = $this->contextProvider->context->outputDir;

        $htmlContent = $this->twigRenderer->render(
            name: $this->getTemplate($MDFile, $collection),
            context: array_merge(
                $MDFile->getFrontMatter(),
                ['content' => $MDFile->getContent()]
            )
        );

        if (false === $this->filesystem->exists(files: $outputDir)) {
            $this->filesystem->mkdir(dirs: $outputDir, mode: 0755);
        }

        $filename = $this->getOutputFilePath(file: $MDFile, collection: $collection, dir: $dir);
        $this->filesystem->dumpFile(filename: $filename, content: $htmlContent);
    }

    private function getTemplate(MDFile $file, Collection $collection): string
    {
        $template = $file->getFrontMatter()['template'] ?? $collection->template;
        $template = sprintf('%s.html.twig', $template ?? 'default_template');

        if (false === $this->filesystem->exists(files: $template)) {
            return 'default_template.html.twig';
        }

        return $template;
    }

    private function getOutputFilePath(MDFile $file, Collection $collection, string $dir): string
    {
        $outputDir = $this->contextProvider->context->outputDir;
        $relativePath = str_replace(search: $dir, replace: '', subject: $file->getRealPath());
        $newFilename = str_replace(search: $file->getExtension(), replace: 'html', subject: $relativePath);

        return $outputDir . '/' . $collection->slug . '/' . $newFilename;
    }
}