<?php

declare(strict_types=1);

namespace JTG\Mark\Repository;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

abstract class FileRepository
{
    private const ALLOWED_PATTERNS = [
        '*.md',
        '*.markdown'
    ];

    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    # region getter methods

    /**
     * @return array<SplFileInfo>
     */
    public function findAll(): array
    {
        $finder = (new Finder())
            ->in(dirs: $this->directory)
            ->name(patterns: self::ALLOWED_PATTERNS)
            ->sortByName();

        return (array) $finder->files()->getIterator();
    }

    # endregion getter methods
}