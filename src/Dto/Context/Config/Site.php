<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Context\Config;

class Site
{
    public function __construct(public readonly string $scheme,
                                public readonly string $host,
                                public readonly int    $port,
                                public readonly string $baseUrl,
                                public readonly string $locale,
                                public readonly string $title,
                                public readonly string $description)
    {
    }
}