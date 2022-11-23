<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use Exception;
use JTG\Mark\DependencyInjection\Compiler\ConfigureContextPass;
use JTG\Mark\DependencyInjection\Compiler\InitializeContextPass;
use JTG\Mark\DependencyInjection\Compiler\RegisterListenersPass;
use JTG\Mark\Event\Container\PostBuildEvent;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class Kernel
{
    use KernelTrait;

    public const ENV_DEV = 'dev';

    private ?ContainerBuilder $container = null;

    private string $env = self::ENV_DEV;
    private bool $booted = false;

    private ?string $markRootDir = null;

    public function __construct(private readonly string $appRootDir)
    {
        $this->loadEnv();
    }

    protected function loadEnv(): void
    {
        $dotEnvFilePath = $this->getAppRootDir() . DIRECTORY_SEPARATOR . '.env';

        if (true === file_exists(filename: $dotEnvFilePath)) {
            (new Dotenv())->load(path: $dotEnvFilePath);
            $this->env = $_ENV['APP_ENV'] ?? self::ENV_DEV;
        }
    }

    /**
     * @throws Exception
     */
    public function boot(): void
    {
        if (null === $this->container) {
            $this->initializeContainer();
        }

        $this
            ->registerParameters()
            ->registerServices()
            ->registerCompilerPasses();

        $this->container->compile();
        $this->booted = true;

        $dispatcher = $this->container->get(id: EventDispatcher::class);
        $dispatcher?->dispatch(
            event: new PostBuildEvent(container: $this->container),
            eventName: PostBuildEvent::NAME
        );
    }

    protected function initializeContainer(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->set(id: 'kernel', service: $this);
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
            'app.env' => $this->getEnv(),
            'app.root_dir' => $this->getAppRootDir(),
            'app.config_file_path' => $this->getAppConfigFile()
        ];
    }

    /**
     * @throws Exception
     */
    protected function registerServices(): Kernel
    {
        $locator = new FileLocator(paths: $this->getMarkConfigDir());

        $loader = new YamlFileLoader(container: $this->container, locator: $locator);
        $loader->load(resource: 'services.yaml');

        return $this;
    }

    private function registerCompilerPasses(): void
    {
        $this->container
            ->addCompilerPass(pass: new RegisterListenersPass())
            ->addCompilerPass(pass: new InitializeContextPass())
            ->addCompilerPass(pass: new ConfigureContextPass());
    }
}