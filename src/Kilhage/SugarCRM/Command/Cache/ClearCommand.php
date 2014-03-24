<?php

namespace Kilhage\SugarCRM\Command\Cache;

use Kilhage\SugarCRM\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ClearCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("cache:clear")
            ->setDescription("Clears the cache");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Clearing Cache");
        $output->writeln("Done");
    }

}
