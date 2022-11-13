<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\HTML;

use JTG\Mark\Model\Markdown\MDFile;
use JTG\Mark\Renderer\Twig\TwigRenderer;
use Symfony\Component\Filesystem\Filesystem;

class HTMLRenderer
{
    private Filesystem $filesystem;

    public function __construct(private readonly TwigRenderer $twigRenderer,
                                private readonly string       $projectOutputDir)
    {
        $this->filesystem = new Filesystem();
    }

    public function render(MDFile $MDFile, string $dir): void
    {
        $htmlContent = $this->twigRenderer->render(
            name: 'default_layout.html.twig',
            context: array_merge(
                $MDFile->getFrontMatter(),
                ['content' => $MDFile->getContent()]
            )
        );

        if (false === $this->filesystem->exists(files: $this->projectOutputDir)) {
            $this->filesystem->mkdir(dirs: $this->projectOutputDir, mode: 0755);
        }

        $this->filesystem->dumpFile(filename: $this->getOutputFilePath(file: $MDFile, dir: $dir), content: $htmlContent);
    }

    private function getOutputFilePath(MDFile $file, string $dir): string
    {
        $relativePath = str_replace(search: $dir, replace: '', subject: $file->getRealPath());
        return $this->projectOutputDir . DIRECTORY_SEPARATOR . str_replace(search: $file->getExtension(), replace: 'html', subject: $relativePath);
    }
}