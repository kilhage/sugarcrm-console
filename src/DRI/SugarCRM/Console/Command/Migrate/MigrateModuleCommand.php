<?php

namespace DRI\SugarCRM\Console\Command\Migrate;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Console\Generator\ModuleCreator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class MigrateModuleCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('migrate:module')
            ->addArgument('module_name', InputArgument::OPTIONAL, 'The module name for the module (or the modules name in in plural)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'If the module already exists, the existing module will be overwritten.')
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'If you provide this argument, nothing will be written to the sugar application.')
            ->setDescription('Migrates a new module to Sugar 7');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module_creator = new ModuleCreator();

        $bean = \BeanFactory::getBean($input->getArgument('module_name'));

        $defs = $GLOBALS['dictionary'][$bean->object_name];

        $args = array (
            'object_name' => $bean->object_name,
            'module_name' => $bean->module_dir,
            'table_name' => $bean->getTableName(),
            'assignable' => in_array('assignable', $defs['templates'], true),
            'team_security' => in_array('team_security', $defs['templates'], true),
            'force' => $input->getOption('force'),
            'dry' => $input->getOption('dry'),
        );

        $module_creator->migrateModule($args);
    }
}
