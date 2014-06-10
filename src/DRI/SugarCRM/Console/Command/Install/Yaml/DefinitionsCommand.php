<?php

namespace DRI\SugarCRM\Console\Command\Install\Yaml;

use \DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class DefinitionsCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("install:yaml:definitions")
            ->setDescription("Installs yaml definitions into compiled php definition files (Not implemented)");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}
