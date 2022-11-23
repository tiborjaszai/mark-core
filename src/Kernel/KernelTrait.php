<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;

trait KernelTrait
{
    /**
     * @throws Exception
     */
    public function getContainer(): ContainerBuilder
    {
        if (false === $this->booted) {
            $this->boot();
        }

        return $this->container;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function getMarkRootDir(): string
    {
        if (null === $this->markRootDir) {
            $this->markRootDir = dirname(path: __DIR__, levels: 2);
        }

        return $this->markRootDir;
    }

    public function getMarkConfigDir(): string
    {
        return $this->getMarkRootDir() . DIRECTORY_SEPARATOR . 'config';
    }

    public function getMarkConfigFile(): string
    {
        return $this->getMarkConfigDir() . DIRECTORY_SEPARATOR . 'config.yaml';
    }

    public function getAppRootDir(): string
    {
        return $this->appRootDir;
    }

    public function getAppConfigFile(): string
    {
        if ('prod' === $this->env) {
            $prodConfigFilePath = $this->getAppRootDir() . DIRECTORY_SEPARATOR . 'config_prod.yaml';

            if (true === file_exists(filename: $prodConfigFilePath)) {
                return $prodConfigFilePath;
            }
        }

        return $this->getAppRootDir() . DIRECTORY_SEPARATOR . 'config.yaml';
    }
}