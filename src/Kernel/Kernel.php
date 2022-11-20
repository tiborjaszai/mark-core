<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;

final class Kernel
{
    use ContextTrait;
    use KernelTrait;

    public const ENV_DEV = 'dev';

    private string $env = self::ENV_DEV;
    private ?ContainerBuilder $container = null;
    private bool $booted = false;

    private ?string $markRootDir = null;

    public function __construct(private readonly string $appRootDir)
    {
        $dotEnvFilePath = $this->getAppRootDir() . DIRECTORY_SEPARATOR . '.env';

        if (true === file_exists(filename: $dotEnvFilePath)) {
            (new Dotenv())->load(path: $dotEnvFilePath);
            $this->env = $_ENV['APP_ENV'] ?? self::ENV_DEV;
        }
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
            'mark.root_dir' => $this->getMarkRootDir(),
            'mark.config_dir' => $this->getMarkConfigDir(),
            'mark.config_file_path' => $this->getMarkConfigFile()
        ];
    }

    protected function getAppParameters(): array
    {
        return [
            'app.root_dir' => $this->getAppRootDir(),
            'app.config_file_path' => $this->getAppConfigFile()
        ];
    }

    /**
     * @throws Exception
     */
    public function registerServices(): Kernel
    {
        $locator = new FileLocator(paths: $this->getMarkConfigDir());

        $loader = new YamlFileLoader(container: $this->container, locator: $locator);
        $loader->load(resource: 'services.yaml');

        return $this;
    }

    private function configureServices(): void
    {
        $this->configureContextProvider();
    }
}