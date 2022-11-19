<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Context\Config;

abstract class Config
{
    public function __construct(public readonly string    $rootDir,
                                protected readonly string $sourceDir,
                                protected readonly string $templatesDir)
    {
    }

    public function getSourceDirPath(): string
    {
        return $this->rootDir . ($this->sourceDir ? DIRECTORY_SEPARATOR . $this->sourceDir : '');
    }

    public function getTemplatesDirPath(): string
    {
        return $this->getSourceDirPath() . ($this->templatesDir ? DIRECTORY_SEPARATOR . $this->templatesDir : '');
    }

    public function getTemplatesDir(): string
    {
        return $this->templatesDir;
    }
}