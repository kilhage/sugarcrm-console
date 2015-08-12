<?php

namespace DRI\SugarCRM\Console\Command\Cache;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class FileMapCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('cache:build-file-map')
            ->setDescription('rebuild the metadata');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Rebuilding file_map.php</info>");

        \SugarAutoLoader::buildCache();

        $output->writeln('<info>Done</info>');
    }
}
