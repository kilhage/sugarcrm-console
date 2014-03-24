<?php

namespace Kilhage\SugarCRM\Command\Generate;

use Kilhage\SugarCRM\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class FieldCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("generate:field")
            ->setDescription("Generates a field");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Generating field");
        $output->writeln("Done");
    }

}
