<?php

declare(strict_types=1);

namespace JTG\Mark\Context;

use JTG\Mark\Dto\Context\Context;

class ContextProvider
{
    public function __construct(private readonly Context $context)
    {
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}