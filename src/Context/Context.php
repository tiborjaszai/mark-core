<?php

declare(strict_types=1);

namespace JTG\Mark\Context;

use JMS\Serializer\Annotation\Type;
use JTG\Mark\Model\Site\Collection;

class Context
{
    public const CONFIG_KERNEL_ROOT_DIR_ALIAS = 'kernel_root_dir';
    public const CONFIG_VENDOR_TEMPLATES_ALIAS = 'vendor_templates_dir';
    public const CONFIG_PROJECT_ROOT_DIR_ALIAS = 'project_root_dir';

    public const IGNORED_LOCAL_CONFIGS = [
        self::CONFIG_KERNEL_ROOT_DIR_ALIAS,
        self::CONFIG_PROJECT_ROOT_DIR_ALIAS,
        self::CONFIG_VENDOR_TEMPLATES_ALIAS
    ];

    #[Type(name: 'array<' . Collection::class . '>')]
    public array $collections = [];

    public function __construct(public readonly string  $kernelRootDir,
                                public readonly string  $vendorTemplatesDir,
                                public readonly string  $projectRootDir,
                                public readonly string  $environment = 'dev',
                                public readonly string  $language = 'en',
                                public readonly string  $baseUrl = 'http://127.0.0.1',
                                public readonly string  $title = 'Mark site',
                                public readonly string  $description = 'Mark site description',
                                private readonly string $distDir = '/dist',
                                private readonly string $dataDir = '/data',
                                private readonly string $collectionsDir = '',
                                private readonly string $templatesDir = '/templates',
                                private readonly string $outputDir = '/build')
    {
    }

    public function getDistDir(): string
    {
        return $this->projectRootDir . $this->distDir;
    }

    public function getDataDir(): string
    {
        return $this->projectRootDir . $this->distDir . $this->dataDir;
    }

    public function getCollectionsDir(): string
    {
        return $this->projectRootDir . $this->distDir . $this->collectionsDir;
    }

    public function getTemplatesDir(): string
    {
        return $this->projectRootDir . $this->distDir . $this->templatesDir;
    }

    public function getOutputDir(): string
    {
        return $this->projectRootDir . $this->outputDir;
    }
}