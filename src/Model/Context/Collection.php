<?php

declare(strict_types=1);

namespace JTG\Mark\Model\Context;

class Collection
{
    public function __construct(public readonly string $name,
                                public readonly string $template,
                                public readonly bool   $output)
    {
    }
}