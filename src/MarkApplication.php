<?php

declare(strict_types=1);

namespace JTG\Mark;

use JTG\Mark\Context\ContextProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class MarkApplication extends Application
{
    public function __construct(iterable        $commands,
                                ContextProvider $contextProvider)
    {
        $markConfig = $contextProvider->context->markConfig;

        parent::__construct($markConfig->appName, $markConfig->appName);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}