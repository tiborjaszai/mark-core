<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Context\Config;

class Config
{
    protected string $distDir = '/dist';
    protected string $templatesDir = '/templates';

    public function __construct(public readonly string $rootDir,
                                string                 $distDir,
                                string                 $templatesDir)
    {
        $this->distDir = $distDir;
        $this->templatesDir = $templatesDir;
    }

    public function getDistDir(): string
    {
        return $this->rootDir . $this->distDir;
    }

    public function getTemplatesDir(): string
    {
        return $this->getDistDir() . $this->templatesDir;
    }
}