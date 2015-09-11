<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class JsGroupingFilesCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('repair:js-groupings')
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
        $output->writeln("<info>Rebuilding JS Groupings files</info>");
        $_REQUEST['js_rebuild_concat'] = 'rebuild';
        $_REQUEST['root_directory'] = SUGAR_PATH;
        require_once 'jssource/minify.php';
    }
}
