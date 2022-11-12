<?php

declare(strict_types=1);

namespace JTG\Mark\Markdown;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class EnvironmentProvider
{
    private Environment $environment;

    public function __construct()
    {
        $this->environment = (new Environment([]))
            ->addExtension(extension: new CommonMarkCoreExtension())
            ->addExtension(extension: new GithubFlavoredMarkdownExtension())
            ->addExtension(extension: new FrontMatterExtension());
    }

    public function get(): Environment
    {
        return $this->environment;
    }
}