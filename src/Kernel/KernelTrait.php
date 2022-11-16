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

    public function getMarkRootDir(): string
    {
        if (null === $this->markRootDir) {
            $this->markRootDir = dirname(path: __DIR__, levels: 2);
        }

        return $this->markRootDir;
    }

    public function getAppRootDir(): string
    {
        return $this->appRootDir;
    }
}