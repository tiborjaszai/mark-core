<?php

declare(strict_types=1);

namespace JTG\Mark\Repository;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

abstract class FileRepository
{
    private const ALLOWED_PATTERNS = [
        '*.md',
        '*.markdown'
    ];

    private string $directory;
    private Filesystem $filesystem;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
        $this->filesystem = new Filesystem();
    }

    # region getter methods

    /**
     * @return array<SplFileInfo>
     */
    public function findAll(): array
    {
        if (false === $this->filesystem->exists(files: $this->directory)) {
            return [];
        }

        $finder = (new Finder())
            ->in(dirs: $this->directory)
            ->name(patterns: self::ALLOWED_PATTERNS)
            ->sortByName();

        return (array) $finder->files()->getIterator();
    }

    # endregion getter methods
}