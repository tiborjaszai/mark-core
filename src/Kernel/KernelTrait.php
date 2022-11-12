<?php

declare(strict_types=1);

namespace JTG\Mark\Kernel;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait KernelTrait
{
    public function getContainer(): ContainerBuilder
    {
        if (false === $this->booted) {
            $this->boot();
        }

        return $this->container;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function getKernelRootDir(): string
    {
        if (null === $this->kernelRootDir) {
            $this->kernelRootDir = dirname(path: __DIR__);
        }

        return $this->kernelRootDir;
    }
}