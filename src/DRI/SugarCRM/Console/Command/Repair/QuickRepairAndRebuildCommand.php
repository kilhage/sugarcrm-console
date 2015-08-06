<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
abstract class QuickRepairAndRebuildCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->addOption(
            'skip-db-upgrade',
            'a',
            InputOption::VALUE_NONE,
            ''
        );

        $this->addOption(
            'output-html',
            'o',
            InputOption::VALUE_NONE,
            ''
        );
    }

    public function getQuickRepairAndRebuild()
    {
        require_once 'modules/Administration/QuickRepairAndRebuild.php';
        require_once 'include/utils/layout_utils.php';

        $repairandclear = new \RepairAndClear();

        return $repairandclear;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getMessage());

        $auto_execute = !$input->getOption('skip-db-upgrade');
        $show_output = $input->getOption('output-html');

        $this->getQuickRepairAndRebuild()->repairAndClearAll(
            $this->getActions(),
            $this->getActions(),
            $auto_execute,
            $show_output,
            ''
        );

        $output->writeln('Done');
    }

    protected function getModules()
    {
    }

    abstract protected function getActions();
    abstract protected function getMessage();
}
