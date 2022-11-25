<?php

declare(strict_types=1);

namespace JTG\Mark\Dto\Context\Config;

class Collection
{
    public function __construct(public readonly string  $name,
                                public readonly bool    $output,
                                public readonly ?string $template = null)
    {
    }
}