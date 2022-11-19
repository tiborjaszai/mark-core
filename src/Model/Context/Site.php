<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Context;

class Site
{
    public function __construct(public readonly string $host,
                                public readonly string $baseUrl,
                                public readonly int    $port,
                                public readonly string $locale,
                                public readonly string $title,
                                public readonly string $description)
    {
    }
}