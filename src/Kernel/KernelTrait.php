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

    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    public function getKernelRootDir(): string
    {
        if (null === $this->kernelRootDir) {
            $this->kernelRootDir = dirname(path: __DIR__, levels: 2);
        }

        return $this->kernelRootDir;
    }
}