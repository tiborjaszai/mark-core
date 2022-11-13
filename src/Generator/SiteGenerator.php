<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Renderer\HTML\HTMLRenderer;
use JTG\Mark\Renderer\Markdown\MarkdownRenderer;
use JTG\Mark\Repository\PostRepository;

class SiteGenerator implements GeneratorInterface
{
    public function __construct(private readonly PostRepository   $postRepository,
                                private readonly MarkdownRenderer $markdownRenderer,
                                private readonly HTMLRenderer     $HTMLRenderer,
                                private readonly string           $projectPostDir)
    {
    }

    public function generate(): bool
    {
        $markdownPostFiles = $this->postRepository->findAll();
        $markdownModels = $this->markdownRenderer->renderFiles(fileInfos: $markdownPostFiles);

        foreach ($markdownModels as $markdownModel) {
            $this->HTMLRenderer->render(MDFile: $markdownModel, dir: $this->projectPostDir);
        }

        return true;
    }
}