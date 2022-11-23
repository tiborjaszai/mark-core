<?php

declare(strict_types=1);

namespace JTG\Mark\Util;

use JTG\Mark\Dto\Context\Context;
use JTG\Mark\Dto\Site\File;

class FileHelper
{
    public static function generateOutputFilePath(Context $context, File $file): string
    {
        $outputDir = $context->appConfig->getOutputDirPath();
        return $outputDir . DIRECTORY_SEPARATOR . $file->getRelativePathname();
    }

    public static function generateHTMLOutputFilePath(Context $context, File $file): string
    {
        $filepath = self::generateOutputFilePath(context: $context, file: $file);
        return str_replace(search: $file->getExtension(), replace: 'html', subject: $filepath);
    }

    public static function generateHTMLOutputPathname(File $file): string
    {
        return str_replace(search: $file->getExtension(), replace: 'html', subject: $file->getRelativePathname());
    }
}