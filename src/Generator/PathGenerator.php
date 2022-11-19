<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Context\Context;
use JTG\Mark\Model\Site\File;

class PathGenerator
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
}