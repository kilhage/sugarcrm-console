<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Emil Kilhage
 */
class ConfigCommand extends ApplicationCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('repair:config')
            ->setDescription('Repairs the database');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $sugar_version;
        $clean_config = loadCleanConfig();
        rebuildConfigFile($clean_config, $sugar_version);
        require_once 'ModuleInstall/ModuleInstaller.php';
        \ModuleInstaller::handleBaseConfig();
    }
}
