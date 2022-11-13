<?php

declare(strict_types=1);

namespace JTG\Mark\Generator;

interface GeneratorInterface
{
    public function generate(): bool;
}