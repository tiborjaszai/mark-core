<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Markdown;

use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use Symfony\Component\Finder\SplFileInfo;

class File
{
    public function __construct(private readonly SplFileInfo                    $fileInfo,
                                private readonly RenderedContentWithFrontMatter $renderedContent)
    {
    }

    public function getRealPath(): string
    {
        return $this->fileInfo->getRealPath();
    }

    public function getExtension(): string
    {
        return $this->fileInfo->getExtension();
    }

    public function getFrontMatter(): array
    {
        return $this->renderedContent->getFrontMatter();
    }

    public function getContent(): string
    {
        return $this->renderedContent->getContent();
    }
}