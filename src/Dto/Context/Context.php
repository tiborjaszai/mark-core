<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Context;

use JMS\Serializer\Annotation\Exclude;
use JTG\Mark\Dto\Context\Config\AppConfig;
use JTG\Mark\Dto\Context\Config\MarkConfig;
use JTG\Mark\Dto\Site\Collection;

class Context
{
    private ?string $env = null;

    #[Exclude]
    private array $collections = [];

    public function __construct(public readonly MarkConfig $markConfig,
                                public readonly AppConfig  $appConfig)
    {
    }

    public function getEnv(): ?string
    {
        return $this->env;
    }

    /**
     * @return array<Collection>
     */
    public function getCollections(): array
    {
        return $this->collections;
    }

    public function getCollectionNames(): array
    {
        return array_keys($this->collections);
    }

    public function getCollection(string $collectionName): ?Collection
    {
        return $this->collections[$collectionName] ?? null;
    }

    public function setCollections(array $collections): Context
    {
        $this->collections = [];

        foreach ($collections as $collection) {
            $this->addCollection(collection: $collection);
        }

        return $this;
    }

    public function addCollection(Collection $collection): Context
    {
        $collectionName = $collection->getName();

        if (false === array_key_exists(key: $collectionName, array: $this->collections)) {
            $this->collections[$collectionName] = $collection;
        }

        return $this;
    }
}