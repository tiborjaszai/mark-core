<?php

declare(strict_types=1);

namespace JTG\Mark\Command;

use JTG\Mark\Generator\SiteGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'mark:generate')]
class MarkGenerateCommand extends Command
{
    private ?SymfonyStyle $io = null;

    public function __construct(private readonly SiteGenerator $generator)
    {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (false === $this->generator->generate()) {
            $this->io->error('Failed to generate the site.');
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}