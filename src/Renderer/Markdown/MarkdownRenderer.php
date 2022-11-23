<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Markdown;

use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Dto\Site\File;
use JTG\Mark\Util\FileHelper;
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

    public function renderFile(Context $context, File $file): ?File
    {
        if (false === in_array($file->getExtension(), self::EXTENSIONS, true)) {
            return $file;
        }

        $renderedContent = $this->convert(input: $file->getContent());

        if ($renderedContent instanceof RenderedContentWithFrontMatter) {
            return $file
                ->setRenderedContent(content: $renderedContent->getContent())
                ->setParams(params: $renderedContent->getFrontMatter())
                ->setOutputFilepath(outputFilepath: FileHelper::generateHTMLOutputFilePath(context: $context, file: $file))
                ->setOutputFilepathname(outputFilepathname: FileHelper::generateHTMLOutputPathname(file: $file));
        }

        return null;
    }
}