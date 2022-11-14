<?php

declare(strict_types=1);

namespace JTG\Mark\Context;

use JMS\Serializer\Annotation\Type;
use JTG\Mark\Model\Site\Collection;

class Context
{
    #[Type(name: 'array<' . Collection::class . '>')]
    public array $collections = [];

    public function __construct(public readonly string $kernelRootDir,
                                public readonly string $projectRootDir,
                                public readonly string $distDir,
                                public readonly string $collectionsDir,
                                public readonly string $dataDir,
                                public readonly string $includesDir,
                                public readonly string $templatesDir,
                                public readonly string $outputDir)
    {
    }
}