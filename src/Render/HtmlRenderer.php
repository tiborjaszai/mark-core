<?php

declare(strict_types=1);

namespace JTG\Mark\Render;

use JTG\Mark\Twig\TwigEnv;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Symfony\Component\Filesystem\Filesystem;

class HtmlRenderer
{
    private TwigEnv $twigEnv;
    private string $outputDir;

    public function __construct(TwigEnv $twigEnv,
                                string  $outputDir)
    {
        $this->twigEnv = $twigEnv;
        $this->outputDir = $outputDir;
    }

    /**
     * @param array<RenderedContentWithFrontMatter> $renderedContents
     * @return void
     */
    public function render(array $renderedContents): void
    {
         $filesystem = new Filesystem();

         foreach ($renderedContents as $filename => $renderedContent) {
             $htmlContent = $this->twigEnv->render(
                 name: 'default_index.html.twig',
                 context: array_merge(
                     $renderedContent->getFrontMatter(),
                     ['content' => $renderedContent->getContent()]
                 )
             );

             if (false === $filesystem->exists($this->outputDir)) {
                 $filesystem->mkdir(dirs: $this->outputDir, mode: 0755);
             }

             $filePath = $this->outputDir . DIRECTORY_SEPARATOR . $filename . '.html';
             $filesystem->dumpFile(filename: $filePath, content: $htmlContent);
         }
    }
}