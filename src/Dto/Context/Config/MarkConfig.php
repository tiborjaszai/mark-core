<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Context\Config;

use JMS\Serializer\Annotation\Type;

class MarkConfig extends Config
{
    public function __construct(public readonly string $appName,
                                public readonly string $appVersion,
                                public readonly string $rootDir,
                                public readonly string $sourceDir,
                                public readonly string $templatesDir,
                                #[Type(name: 'array<string>')]
                                public readonly array  $yamlExtensions = [])
    {
        parent::__construct($this->rootDir, $this->sourceDir, $this->templatesDir);
    }
}