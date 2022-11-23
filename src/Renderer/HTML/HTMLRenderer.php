<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\HTML;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Dto\Site\File;
use JTG\Mark\Renderer\Twig\TwigRenderer;
use Symfony\Component\Filesystem\Filesystem;

class HTMLRenderer
{
    private ?Context $context;
    private Filesystem $filesystem;

    public function __construct(private readonly ContextProvider $contextProvider,
                                private readonly TwigRenderer    $twigRenderer)
    {
        $this->context = $this->contextProvider->getContext();
        $this->filesystem = new Filesystem();
    }

    public function render(File $file): void
    {
        $htmlContent = $this->twigRenderer->render(
            name: $this->getTemplate(file: $file),
            context: [
                'node' => array_merge(
                    $file->getParams(),
                    ['content' => $file->getRenderedContent()]
                )
            ]
        );

        $this->filesystem->dumpFile(filename: $file->getOutputFilepath(), content: $htmlContent);
    }

    private function getTemplate(File $file): string
    {
        $template = sprintf('%s.html.twig', $file->getParams()['template'] ?? $file->getCollection()?->getTemplate());
        $templatePath = $this->context->appConfig->getTemplatesDirPath() . DIRECTORY_SEPARATOR . $template;

        if (false === $this->filesystem->exists(files: $templatePath)) {
            return 'default_template.html.twig';
        }

        return $template;
    }
}