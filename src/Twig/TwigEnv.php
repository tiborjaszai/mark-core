<?php

declare(strict_types=1);

namespace JTG\Mark\Twig;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class TwigEnv
{
    private ?Environment $env;
    private array $templateDirs;

    public function __construct(array $templateDirs)
    {
        $loader = new FilesystemLoader(paths: $templateDirs);
        $this->env = new Environment(loader: $loader, options: ['autoescape' => false]);
        $this->templateDirs = $templateDirs;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function render(string $name, array $context = []): string
    {
        return $this->env->render(name: $name, context: $context);
    }

    public function getTemplateDirs(): array
    {
        return $this->templateDirs;
    }
}