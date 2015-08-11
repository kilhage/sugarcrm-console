<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Console\Command\SetOwnerCommand;
use DRI\SugarCRM\Console\Command\SetPermCommand;
use Symfony\Component\Console\Input\ArgvInput;
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

        $this->addOption(
            'skip-set-owner',
            null,
            InputOption::VALUE_NONE,
            ''
        );

        $this->addOption(
            'skip-set-perm',
            null,
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

        if (!$input->getOption('skip-set-owner')) {
            $i = new ArgvInput(array ('bin/sugarcrm', 'set:owner'));
            $cmd = new SetOwnerCommand();
            $cmd->setApplication($this->getApplication());
            $cmd->setSugar($this->getSugar());
            $cmd->run($i, $output);
        }

        if (!$input->getOption('skip-set-perm')) {
            $i = new ArgvInput(array ('bin/sugarcrm', 'set:perm'));
            $cmd = new SetPermCommand();
            $cmd->setApplication($this->getApplication());
            $cmd->setSugar($this->getSugar());
            $cmd->run($i, $output);
        }

        $output->writeln('<info>Done</info>');
    }

    protected function getModules()
    {
    }

    abstract protected function getActions();
    abstract protected function getMessage();
}
