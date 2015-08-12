<?php

namespace DRI\SugarCRM\Console\Command\Metadata;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class SetupCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('metadata:setup')
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
        $output->writeln("<info>Rebuilding metadata</info>");

        \MetaDataManager::setupMetadata();

        $output->writeln('<info>Done</info>');
    }
}
