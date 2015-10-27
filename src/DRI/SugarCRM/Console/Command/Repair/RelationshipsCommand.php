<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class RelationshipsCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('repair:relationships')
            ->setDescription('Repairs the JS Grouping files');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Rebuilding relationships</info>");
        ob_start();
        require 'modules/Administration/RebuildRelationship.php';
        $content = ob_get_clean();
        $content = preg_replace('/<br>/i', "\n", $content);
        $output->writeln("<comment>$content</comment>");
    }
}
