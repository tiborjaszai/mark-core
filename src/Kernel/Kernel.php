<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class Kernel
{
    use KernelTrait;

    private ?ContainerBuilder $container = null;
    private bool $booted = false;

    private ?string $kernelRootDir = null;

    public function __construct(private readonly string $projectDir,
                                private readonly string $environment = 'dev')
    {
    }

    public function boot(): void
    {
        if (null === $this->container) {
            $this->initializeContainer();
        }

        $this
            ->registerParameters()
            ->registerServices();

        $this->container->compile();

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
            ->add(array_merge(
                $this->getKernelParameters(),
                $this->getProjectParameters()
            ));

        return $this;
    }

    protected function getKernelParameters(): array
    {
        return [
            'kernel.root_dir' => $this->getKernelRootDir()
        ];
    }

    protected function getProjectParameters(): array
    {
        return [
            'project.root_dir' => $this->getProjectDir(),
            'project.environment' => $this->getEnvironment()
        ];
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