<?php

declare(strict_types=1);

namespace JTG\Mark\Repository;

use JTG\Mark\Dto\Context\Context;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileRepository
{
    private ?Context $context;
    private Filesystem $filesystem;

    public function __construct(?Context $context = null)
    {
        $this->context = $context;
        $this->filesystem = new Filesystem();
    }

    # region getter methods

    /**
     * @return array<SplFileInfo>
     */
    public function findAll(): array
    {
        if (null === $this->context) {
            return [];
        }

        $config = $this->context->appConfig;
        $sourceDirPath = $config->getSourceDirPath();

        if (false === $this->filesystem->exists(files: $sourceDirPath)) {
            return [];
        }

        $finder = (new Finder())
            ->in(dirs: $sourceDirPath)
            ->ignoreVCS(ignoreVCS: true)
            ->ignoreDotFiles(ignoreDotFiles: false);

        // Exclude dirs
        foreach ($config->excludeDirs as $excludeDir) {
            $finder->exclude(dirs: $excludeDir);
        }

        // Exclude template dir
        $finder->exclude(dirs: $config->templatesDir);

        // Exclude files
        foreach ($config->excludeFiles as $excludeFile) {
            $finder->files()->notName(patterns: $excludeFile);
        }

        $finder->sortByName();

        return (array) $finder->files()->getIterator();
    }

    # endregion getter methods
}