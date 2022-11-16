<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class Kernel
{
    use ContextTrait;
    use KernelTrait;

    private ?ContainerBuilder $container = null;
    private bool $booted = false;

    private ?string $markRootDir = null;

    public function __construct(private readonly string $appRootDir)
    {
    }

    public function boot(): void
    {
        if (null === $this->container) {
            $this->initializeContainer();
        }

        $this
            ->registerParameters()
            ->registerServices()
            ->configureServices();

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
                $this->getMarkParameters(),
                $this->getAppParameters()
            ));

        return $this;
    }

    protected function getMarkParameters(): array
    {
        return [
            'mark.root_dir' => $this->getMarkRootDir()
        ];
    }

    protected function getAppParameters(): array
    {
        return [
            'app.root_dir' => $this->getAppRootDir()
        ];
    }

    /**
     * @throws Exception
     */
    public function registerServices(): Kernel
    {
        $locator = new FileLocator(paths: $this->getMarkRootDir() . '/config');

        $loader = new YamlFileLoader(container: $this->container, locator: $locator);
        $loader->load(resource: 'services.yaml');

        return $this;
    }

    private function configureServices(): void
    {
        $this->configureContextProvider();
    }
}