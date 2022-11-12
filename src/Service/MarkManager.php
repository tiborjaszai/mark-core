<?php

declare(strict_types=1);

namespace JTG\Mark\Service;

use JTG\Mark\Markdown\MarkdownConverter;
use JTG\Mark\Render\HtmlRenderer;
use JTG\Mark\Repository\PostRepository;

class MarkManager
{
    private PostRepository $postRepository;
    private MarkdownConverter $markdownConverter;
    private HtmlRenderer $htmlRenderer;

    public function __construct(PostRepository    $postRepository,
                                MarkdownConverter $markdownConverter,
                                HtmlRenderer      $htmlRenderer)
    {
        $this->postRepository = $postRepository;
        $this->markdownConverter = $markdownConverter;
        $this->htmlRenderer = $htmlRenderer;
    }

    public function doProcess(): void
    {
        $markdownPostFiles = $this->postRepository->findAll();
        $renderedContents = $this->markdownConverter->parseFiles($markdownPostFiles);

        $this->htmlRenderer->render($renderedContents);
    }
}