<?php

namespace Kilhage\SugarCRM\Command\Scheduler;

use Kilhage\SugarCRM\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ConfigureCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("scheduler:configure")
            ->setDescription("Configures a scheduler");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Configuring Scheduler");
        $output->writeln("Done");
    }

}
