<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Site;

class Collection
{
    public function __construct(public readonly string $name,
                                public readonly string $slug,
                                public readonly string $template = 'default_template',
                                public readonly bool   $output = true)
    {
    }
}