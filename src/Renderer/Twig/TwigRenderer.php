<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Twig;

use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    private Context $context;
    private Environment $env;

    public function __construct(ContextProvider $contextProvider)
    {
        $this->context = $contextProvider->context;

        $this->initTwigEnv($this->getTemplateDirs());
    }

    private function getTemplateDirs(): array
    {
        $filesystem = new Filesystem();
        $appConfig = $this->context->appConfig;

        $templateDirs = [$this->context->markConfig->getTemplatesDir()];

        if (true === $filesystem->exists(files: $appConfig->getTemplatesDir())) {
            $templateDirs[] = $appConfig->getTemplatesDir();
        }

        return $templateDirs;
    }

    private function initTwigEnv(array $templateDirs): void
    {
        $this->env = new Environment(
            loader: new FilesystemLoader(paths: $templateDirs),
            options: ['autoescape' => false]
        );
    }

    public function render(string $name, array $context = []): string
    {
        return $this->env->render(
            name: $name,
            context: array_merge(['context' => $this->context], $context)
        );
    }
}