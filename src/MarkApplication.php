<?php

declare(strict_types=1);

namespace JTG\Mark;

use JTG\Mark\Context\ContextProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class MarkApplication extends Application
{
    public function __construct(iterable                         $commands,
                                private readonly ContextProvider $contextProvider)
    {
        $markConfig = $contextProvider->getContext()->markConfig;

        parent::__construct(name: $markConfig->appName, version: $markConfig->appVersion);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->add(command: $command);
        }
    }
}