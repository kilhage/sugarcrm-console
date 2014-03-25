<?php

namespace Kilhage\SugarCRM\Command\Scheduler\Job;

use Kilhage\SugarCRM\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class RunCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("scheduler:job:run")
            ->setDescription("Executes a scheduler job");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("");
        $output->writeln("Done");
    }

}
