<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Twig;

use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    private Environment $env;

    public function __construct(string $kernelTemplateDir, string $projectTemplateDir)
    {
        $dirs = $this->checkTemplateDirs(kernelTemplateDir: $kernelTemplateDir, projectTemplateDir: $projectTemplateDir);
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