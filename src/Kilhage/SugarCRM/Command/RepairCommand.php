<?php

namespace Kilhage\SugarCRM\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class RepairCommand extends RepairAbstractCommand
{

    protected function configure()
    {
        $this->setName("repair");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Running Quick Repair & Rebuild");
        $output->writeln("Done");
    }

}
