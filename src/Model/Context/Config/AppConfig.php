<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Context\Config;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JTG\Mark\Model\Context\CollectionType;
use JTG\Mark\Model\Context\Site;

class AppConfig extends Config
{
    private string $dataDir = '/data';
    private string $collectionsDir = '';
    private string $outputDir = '/build';

    #[SerializedName(name: 'collections')]
    #[Type(name: 'array<' . CollectionType::class . '>')]
    private array $collectionTypes = [];

    public function __construct(public readonly string $rootDir,
                                public readonly Site   $site,
                                string                 $distDir,
                                string                 $templatesDir,
                                string                 $dataDir,
                                string                 $collectionsDir,
                                string                 $outputDir,
                                public readonly string $environment = 'dev',
                                public readonly string $language = 'en')
    {
        parent::__construct($this->rootDir, $distDir, $templatesDir);

        $this->dataDir = $dataDir;
        $this->collectionsDir = $collectionsDir;
        $this->outputDir = $outputDir;
    }

    public function getDataDir(): string
    {
        return $this->getDistDir() . $this->dataDir;
    }

    public function getCollectionsDir(): string
    {
        return $this->getDistDir() . $this->collectionsDir;
    }

    public function getOutputDir(): string
    {
        return $this->rootDir . $this->outputDir;
    }

    /**
     * @return array<CollectionType>
     */
    public function getCollectionTypes(): array
    {
        return $this->collectionTypes;
    }
}