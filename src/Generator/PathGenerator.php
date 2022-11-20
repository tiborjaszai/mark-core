<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

use JTG\Mark\Context\Context;
use JTG\Mark\Model\Site\File;

class PathGenerator
{
    // TODO: Path builder service...

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

    public static function generateHTMLOutputURL(Context $context, File $file): string
    {
        $siteConfig = $context->appConfig->site;

        $filepathname = $file->getRelativePathname();
        $htmlFilepathname = str_replace(search: $file->getExtension(), replace: 'html', subject: $filepathname);

        return sprintf(
            '%s%s/%s%s',
            $siteConfig->host,
            80 !== $siteConfig->port ? (':' . $siteConfig->port) : '',
            false === empty($siteConfig->baseUrl) ? ($siteConfig->baseUrl . '/') : '',
            $htmlFilepathname
        );
    }
}