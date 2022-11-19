<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\HTML;

use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Generator\PathGenerator;
use JTG\Mark\Model\Site\File;
use JTG\Mark\Renderer\Twig\TwigRenderer;
use Symfony\Component\Filesystem\Filesystem;

class HTMLRenderer
{
    private Filesystem $filesystem;
    private Context $context;

    public function __construct(ContextProvider               $contextProvider,
                                private readonly TwigRenderer $twigRenderer)
    {
        $this->context = $contextProvider->context;
        $this->filesystem = new Filesystem();
    }

    public function render(File $file): void
    {
        $htmlContent = $this->twigRenderer->render(
            name: $this->getTemplate(file: $file),
            context: [
                'node' => array_merge(
                    $file->getParams(),
                    ['content' => $file->getContent()]
                )
            ]
        );

        $filename = PathGenerator::generateHTMLOutputFilePath(context: $this->context, file: $file);
        $this->filesystem->dumpFile(filename: $filename, content: $htmlContent);
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