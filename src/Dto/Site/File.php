<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Site;

use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Util\FileHelper;
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
    private ?string $renderedContent = null;
    private array $params = [];

    private ?string $outputFilepath = null;
    private ?string $outputFilepathname = null;

    public static function fromFileInfo(Context $context, SplFileInfo $fileInfo): File
    {
        $file = (new self())
            ->setFilename(filename: $fileInfo->getFilename())
            ->setFilenameWithoutExtension(filenameWithoutExtension: $fileInfo->getFilenameWithoutExtension())
            ->setExtension(extension: $fileInfo->getExtension())
            ->setRelativePath(relativePath: $fileInfo->getRelativePath())
            ->setRelativePathname(relativePathname: $fileInfo->getRelativePathname())
            ->setAbsolutePath(absolutePath: $fileInfo->getRealPath())
            ->setContent(content: $fileInfo->getContents());

        $file->setOutputFilepath(outputFilepath: FileHelper::generateOutputFilePath(context: $context, file: $file));

        return $file;
    }

    # region getters

    public function getId(): ?string
    {
        return $this->getRelativePathname();
    }

    public function getRouteId(): string
    {
        return $this->getOutputFilepathname();
    }

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

    public function getRenderedContent(): ?string
    {
        return $this->renderedContent;
    }

    public function setRenderedContent(?string $content): File
    {
        $this->renderedContent = $content;
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

    public function getOutputFilepath(): ?string
    {
        return $this->outputFilepath;
    }

    public function setOutputFilepath(?string $outputFilepath): File
    {
        $this->outputFilepath = $outputFilepath;
        return $this;
    }

    public function getOutputFilepathname(): ?string
    {
        return $this->outputFilepathname ?? $this->getRelativePathname();
    }

    public function setOutputFilepathname(?string $outputFilepathname): File
    {
        $this->outputFilepathname = $outputFilepathname;
        return $this;
    }

    # endregion getters
}