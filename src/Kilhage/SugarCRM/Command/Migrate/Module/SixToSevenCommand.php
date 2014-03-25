<?php

namespace Kilhage\SugarCRM\Command\Migrate\Module;

use Kilhage\SugarCRM\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class SixToSevenCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("migrate:module:6-to-7")
            ->setDescription("Migrates a module from 6 to 7");
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
