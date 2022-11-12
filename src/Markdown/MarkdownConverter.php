<?php

declare(strict_types=1);

namespace JTG\Mark\Markdown;

use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter as BaseMarkdownConverter;
use Symfony\Component\Finder\SplFileInfo;

class MarkdownConverter extends BaseMarkdownConverter
{
    public function __construct(EnvironmentProvider $provider)
    {
        parent::__construct(environment: $provider->get());
    }

    public function parseFile(SplFileInfo $fileInfo): ?RenderedContentWithFrontMatter
    {
        $result = $this->convert(input: $fileInfo->getContents());

        if ($result instanceof RenderedContentWithFrontMatter) {
            return $result;
        }

        return null;
    }
}