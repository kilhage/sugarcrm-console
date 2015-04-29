<?php

namespace DRI\SugarCRM\Console\Command\Module\Vardefs\Yml;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class GenerateCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('module:vardefs:yml:generate')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'The name of the module that you want to add the yaml vardef file for')
            ->addArgument('fileName', InputArgument::OPTIONAL, 'The name of the yaml vardef file that you want to add')
            ->setDescription('Generates yaml definition files into a module (Not implemented)');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
