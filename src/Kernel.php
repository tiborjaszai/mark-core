<?php

declare(strict_types=1);

namespace JTG\Mark;

use Exception;
use JTG\Mark\Kernel\KernelTrait;
use JTG\Mark\Markdown\MarkdownConverter;
use JTG\Mark\Render\HtmlRenderer;
use JTG\Mark\Repository\PostRepository;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Finder\SplFileInfo;

final class Kernel
{
    use KernelTrait;

    private ?ContainerBuilder $container = null;
    private string $environment;
    private bool $booted = false;

    private ?string $kernelRootDir = null;
    private string $projectDir;

    public function __construct(string $projectDir, string $environment = 'dev')
    {
        $this->projectDir = $projectDir;
        $this->environment = $environment;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        $this->boot();
        $this->renderHtmlPages();
    }

    protected function renderHtmlPages(): void
    {
        $container = $this->getContainer();

        /** @var array<SplFileInfo> $postFiles */
        $markdownPostFiles = $container->get(PostRepository::class)?->findAll();
        $renderedContents = $container->get(MarkdownConverter::class)?->parseFiles($markdownPostFiles);

        $container->get(HtmlRenderer::class)?->render($renderedContents);
    }

    /**
     * @throws Exception
     */
    protected function boot(): void
    {
        if (null === $this->container) {
            $this->initializeContainer();
        }

        $this
            ->registerParameters()
            ->registerServices();

        $this->booted = true;
    }

    protected function initializeContainer(): void
    {
        $this->container = new ContainerBuilder();
    }

    protected function registerParameters(): Kernel
    {
        $this
            ->container
            ->getParameterBag()
            ->add([
                'mark.kernel.root_dir' => $this->getKernelRootDir(),
                'mark.project.root_dir' => $this->getProjectDir(),
                'mark.project.environment' => $this->getEnvironment()
            ]);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function registerServices(): Kernel
    {
        $locator = new FileLocator(paths: $this->getKernelRootDir() . '/config');

        $loader = new YamlFileLoader(container: $this->container, locator: $locator);
        $loader->load(resource: 'services.yaml');

        return $this;
    }
}