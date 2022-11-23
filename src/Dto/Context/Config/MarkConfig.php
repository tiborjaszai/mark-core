<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Context\Config;

class MarkConfig extends Config
{
    public function __construct(public readonly string $appName,
                                public readonly string $appVersion,
                                public readonly string $rootDir,
                                public readonly string $sourceDir,
                                public readonly string $templatesDir)
    {
        parent::__construct($this->rootDir, $this->sourceDir, $this->templatesDir);
    }
}