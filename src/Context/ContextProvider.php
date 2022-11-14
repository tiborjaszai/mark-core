<?php

declare(strict_types=1);

namespace JTG\Mark\Context;

class ContextProvider
{
    public function __construct(public readonly Context $context)
    {
    }
}