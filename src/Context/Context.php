<?php

declare(strict_types=1);

namespace JTG\Mark\Context;

use JTG\Mark\Model\Context\Config\AppConfig;
use JTG\Mark\Model\Context\Config\Config;

class Context
{
    public function __construct(public readonly Config    $markConfig,
                                public readonly AppConfig $appConfig)
    {
    }
}