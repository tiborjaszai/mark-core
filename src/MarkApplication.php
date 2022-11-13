<?php

declare(strict_types=1);

namespace JTG\Mark;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class MarkApplication extends Application
{
    private const APP_NAME = 'Mark';
    private const APP_VERSION = 'v0.1';

    public function __construct(iterable $commands)
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        /** @var Command $command */
        foreach ($commands as $command) {
            $this->add($command);
        }
    }
}