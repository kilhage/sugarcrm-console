<?php

namespace Kilhage\SugarCRM\Command\Install;

use Kilhage\SugarCRM\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class PackageCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("install:package")
            ->setDescription("Installs a package");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Installing package");
        $output->writeln("Done");
    }

}
