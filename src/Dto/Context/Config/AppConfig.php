<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Context\Config;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class AppConfig extends Config
{
    #[Type(name: 'array<string>')]
    public readonly array $excludeDirs;

    #[Type(name: 'array<string>')]
    public readonly array $excludeFiles;

    #[SerializedName(name: 'collections')]
    #[Type(name: 'array<' . Collection::class . '>')]
    private array $collections = [];

    public function __construct(public readonly string  $rootDir,
                                public readonly Site    $site,
                                public readonly string  $sourceDir,
                                private readonly string $dataDir,
                                private readonly string $collectionsDir,
                                public readonly string  $templatesDir,
                                private readonly string $outputDir,
                                public readonly bool    $safe,
                                public readonly string  $encoding,
                                public readonly string  $permalink,
                                public readonly bool    $enablePagination,
                                public readonly string  $paginatePath)
    {
        parent::__construct($this->rootDir, $this->sourceDir, $this->templatesDir);
    }

    public function getDataDirPath(): string
    {
        return $this->getSourceDirPath() . ($this->dataDir ? DIRECTORY_SEPARATOR . $this->dataDir : '');
    }

    public function getCollectionsDirPath(): string
    {
        return $this->getSourceDirPath() . ($this->collectionsDir ? DIRECTORY_SEPARATOR . $this->collectionsDir : '');
    }

    public function getOutputDirPath(): string
    {
        return $this->rootDir . ($this->outputDir ? DIRECTORY_SEPARATOR . $this->outputDir : '');
    }

    /**
     * @return array<Collection>
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    public function getCollection(?string $name, bool $defaultRoot = false): ?Collection
    {
        switch (true) {
            case null === $name && true === $defaultRoot:
                $name = 'root';
                break;

            case null === $name && false === $defaultRoot:
                return null;
        }

        foreach ($this->getCollections() as $collection) {
            if ($name === $collection->name) {
                return $collection;
            }
        }

        return null;
    }

    public function addCollection(Collection $collection): AppConfig
    {
        if (null === $this->getCollection($collection->name)) {
            $this->collections[] = $collection;
        }

        return $this;
    }
}