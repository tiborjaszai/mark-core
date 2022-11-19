<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Site;

use Symfony\Component\Finder\SplFileInfo;

class File
{
    private ?Collection $collection = null;

    private ?string $filename;
    private ?string $filenameWithoutExtension;
    private ?string $extension;
    private ?string $relativePath;
    private ?string $relativePathname;
    private ?string $absolutePath;

    private ?string $content = null;
    private array $params = [];

    public static function fromFileInfo(SplFileInfo $fileInfo): File
    {
        return (new self())
            ->setFilename($fileInfo->getFilename())
            ->setFilenameWithoutExtension($fileInfo->getFilenameWithoutExtension())
            ->setExtension($fileInfo->getExtension())
            ->setRelativePath($fileInfo->getRelativePath())
            ->setRelativePathname($fileInfo->getRelativePathname())
            ->setAbsolutePath($fileInfo->getRealPath())
            ->setContent($fileInfo->getContents());
    }

    # region getters

    public function getCollection(): ?Collection
    {
        return $this->collection;
    }

    public function setCollection(?Collection $collection): File
    {
        $this->collection = $collection;
        return $this;
    }

    public function getCollectionName(): ?string
    {
        if ('' === $this->getRelativePath()) {
            return null;
        }

        return explode(separator: DIRECTORY_SEPARATOR, string: $this->getRelativePath())[0] ?? null;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): File
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilenameWithoutExtension(): ?string
    {
        return $this->filenameWithoutExtension;
    }

    public function setFilenameWithoutExtension(?string $filenameWithoutExtension): File
    {
        $this->filenameWithoutExtension = $filenameWithoutExtension;
        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): File
    {
        $this->extension = $extension;
        return $this;
    }

    public function getRelativePath(): ?string
    {
        return $this->relativePath;
    }

    public function setRelativePath(?string $relativePath): File
    {
        $this->relativePath = $relativePath;
        return $this;
    }

    public function getRelativePathname(): ?string
    {
        return $this->relativePathname;
    }

    public function setRelativePathname(?string $relativePathname): File
    {
        $this->relativePathname = $relativePathname;
        return $this;
    }

    public function getAbsolutePath(): ?string
    {
        return $this->absolutePath;
    }

    public function setAbsolutePath(?string $absolutePath): File
    {
        $this->absolutePath = $absolutePath;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): File
    {
        $this->content = $content;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): File
    {
        $this->params = $params;
        return $this;
    }

    # endregion getters
}