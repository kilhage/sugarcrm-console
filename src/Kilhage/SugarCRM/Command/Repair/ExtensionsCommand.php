<?php

namespace Kilhage\SugarCRM\Command\Repair;

use Kilhage\SugarCRM\Command\RepairAbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ExtensionsCommand extends RepairAbstractCommand
{

    protected function configure()
    {
        $this->setName("repair:ext");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Rebuilding Extensions");
        $output->writeln("Done");
    }

}
