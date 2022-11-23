<?php

declare(strict_types=1);

namespace JTG\Mark\Renderer\Twig;

use JTG\Mark\Context\ContextProvider;
use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Kernel\Kernel;
use JTG\Mark\Routing\UrlGenerator;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class TwigRenderer
{
    private ?Context $context = null;
    private ?Environment $env = null;

    public function __construct(ContextProvider               $contextProvider,
                                private readonly UrlGenerator $urlGenerator)
    {
        $this->context = $contextProvider->getContext();
        $this->initTwigEnv(templateDirs: $this->getTemplateDirs());
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

    private function getTemplateDirs(): array
    {
        $appConfig = $this->context->appConfig;
        $templateDirs = [$this->context->markConfig->getTemplatesDirPath()];

        if (true === (new Filesystem())->exists(files: $appConfig->getTemplatesDirPath())) {
            $templateDirs[] = $appConfig->getTemplatesDirPath();
        }

        return $templateDirs;
    }

    public function render(string $name, array $context = []): string
    {
        return $this->env->render(
            name: $name,
            context: array_merge(
                [
                    'context' => $this->context,
                    'urlGenerator' => $this->urlGenerator
                ],
                $context
            )
        );
    }
}