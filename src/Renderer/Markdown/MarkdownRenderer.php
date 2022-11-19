<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Markdown;

use JTG\Mark\Model\Site\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownRenderer extends MarkdownConverter
{
    public const EXTENSIONS = [
        'md',
        'markdown'
    ];

    public function __construct()
    {
        $environment = (new Environment([]))
            ->addExtension(extension: new CommonMarkCoreExtension())
            ->addExtension(extension: new GithubFlavoredMarkdownExtension())
            ->addExtension(extension: new FrontMatterExtension());

        parent::__construct(environment: $environment);
    }

    public function renderFile(File $file): ?File
    {
        $renderedContent = $this->convert(input: $file->getContent());

        if ($renderedContent instanceof RenderedContentWithFrontMatter) {
            return $file
                ->setContent($renderedContent->getContent())
                ->setParams($renderedContent->getFrontMatter());
        }

        return null;
    }
}