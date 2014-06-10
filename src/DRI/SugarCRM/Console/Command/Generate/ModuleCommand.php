<?php

namespace DRI\SugarCRM\Console\Command\Generate;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ModuleCommand extends ApplicationCommand
{

    /**
     * 
     */
    protected function configure()
    {
        $this->setName("generate:module")
            ->addArgument('objectName', InputOption::REQUIRED, '', null)
            ->addArgument('moduleName', InputOption::OPTIONAL, '', null)
            ->addArgument('tableName', InputOption::OPTIONAL, '', null)
            ->addOption('importable', InputArgument::OPTIONAL, '', 'true')
            ->addOption('audited', InputArgument::OPTIONAL, '', 'true')
            ->addOption('template', InputArgument::OPTIONAL, '', 'basic')
            ->addOption('assignable', InputArgument::OPTIONAL, '', 'true')
            ->addOption('teamSecurity', InputArgument::OPTIONAL, '', 'true')
            ->setDescription("Generates a module (Not implemented)");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $objectName = $input->getArgument('objectName');
        $moduleName = $input->getArgument('moduleName');
        $tableName = $input->getArgument('tableName');

        $importable = $this->parseBoolOption($input->getOption('importable'));
        $audited = $this->parseBoolOption($input->getOption('audited'));
        $template = $input->getOption('template');
        $assignable = $this->parseBoolOption($input->getOption('assignable'));
        $teamSecurity = $this->parseBoolOption($input->getOption('teamSecurity'));
    }

    private function parseBoolOption($value)
    {
        return $value == 'true';
    }

}
