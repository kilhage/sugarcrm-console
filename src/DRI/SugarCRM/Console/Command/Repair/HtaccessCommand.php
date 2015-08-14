<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class HtaccessCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('repair:htaccess')
            ->setDescription('Clears tpls');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Repairing .htaccess</info>");
        require_once "install/install_utils.php";
        handleHtaccess();
        $output->writeln("<info>Done</info>");
    }
}
