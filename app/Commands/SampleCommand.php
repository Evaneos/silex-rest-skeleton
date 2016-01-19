<?php

namespace Evaneos\REST\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SampleCommand extends Command
{
    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this->setDescription('Sample command');
    }

    /**
     * Code executed when command invoked.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Command executed</info>');
    }
}
