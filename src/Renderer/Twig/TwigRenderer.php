<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Twig;

use JTG\Mark\Context\Context;
use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Kernel\Kernel;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Extension\DebugExtension;
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
        $appConfig = $this->context->appConfig;
        $templateDirs = [$this->context->markConfig->getTemplatesDirPath()];

        if (true === (new Filesystem())->exists(files: $appConfig->getTemplatesDirPath())) {
            $templateDirs[] = $appConfig->getTemplatesDirPath();
        }

        return $templateDirs;
    }

    private function initTwigEnv(array $templateDirs): void
    {
        $this->env = new Environment(
            loader: new FilesystemLoader(paths: $templateDirs),
            options: [
                'autoescape' => $this->context->appConfig->safe,
                'debug' => Kernel::ENV_DEV === $this->context->getEnv()

            ]
        );

        if (Kernel::ENV_DEV === $this->context->getEnv()) {
            $this->env->addExtension(new DebugExtension());
        }
    }

    public function render(string $name, array $context = []): string
    {
        return $this->env->render(
            name: $name,
            context: array_merge(['context' => $this->context], $context)
        );
    }
}