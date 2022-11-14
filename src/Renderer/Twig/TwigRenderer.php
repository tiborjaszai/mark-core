<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Twig;

use JTG\Mark\Context\ContextProvider;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    private Environment $env;

    public function __construct(ContextProvider $contextProvider)
    {
        $context = $contextProvider->context;

        $dirs = $this->checkTemplateDirs(kernelTemplateDir: $context->kernelRootDir . '/dist/templates', projectTemplateDir: $context->templatesDir);
        $this->initTwigEnv($dirs);
    }

    private function checkTemplateDirs(string $kernelTemplateDir, string $projectTemplateDir): array
    {
        $filesystem = new Filesystem();
        $templateDirs = [$kernelTemplateDir];

        if (true === $filesystem->exists(files: $projectTemplateDir)) {
            $templateDirs[] = $projectTemplateDir;
        }

        return $templateDirs;
    }

    private function initTwigEnv(array $templateDirs): void
    {
        $this->env = new Environment(loader: new FilesystemLoader(paths: $templateDirs), options: ['autoescape' => false]);
    }

    public function render(string $name, array $context = []): string
    {
        return $this->env->render(name: $name, context: $context);
    }
}