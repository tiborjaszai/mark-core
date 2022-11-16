<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Markdown;

use JTG\Mark\Model\Markdown\File;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\MarkdownConverter;
use Symfony\Component\Finder\SplFileInfo;

class MarkdownRenderer extends MarkdownConverter
{
    public function __construct(EnvironmentProvider $provider)
    {
        parent::__construct(environment: $provider->get());
    }

    public function renderFile(SplFileInfo $fileInfo): ?File
    {
        $renderedContent = $this->convert(input: $fileInfo->getContents());

        if ($renderedContent instanceof RenderedContentWithFrontMatter) {
            return new File(fileInfo: $fileInfo, renderedContent: $renderedContent);
        }

        return null;
    }

    /**
     * @param array<SplFileInfo> $fileInfos
     * @return array<File>
     */
    public function renderFiles(array $fileInfos): array
    {
        $models = [];

        foreach ($fileInfos as $fileInfo) {
            if ($mdFileModel = $this->renderFile(fileInfo: $fileInfo)) {
                $models[] = $mdFileModel;
            }
        }

        return $models;
    }
}