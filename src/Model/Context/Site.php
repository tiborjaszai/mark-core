<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Context;

class Site
{
    public function __construct(public readonly string $baseUrl = 'http://127.0.0.1',
                                public readonly string $title = 'Mark site',
                                public readonly string $description = 'Mark site description')
    {
    }
}