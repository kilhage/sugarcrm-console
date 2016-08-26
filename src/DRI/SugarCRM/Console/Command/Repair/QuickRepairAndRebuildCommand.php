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
            'owner',
            'O',
            InputOption::VALUE_NONE,
            ''
        );

        $this->addOption(
            'perm',
            'P',
            InputOption::VALUE_NONE,
            ''
        );
    }

    public function getQuickRepairAndRebuild()
    {
        require_once 'modules/Administration/QuickRepairAndRebuild.php';
        require_once 'include/utils/layout_utils.php';
        return new \RepairAndClear();
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
            $this->getModules(),
            $auto_execute,
            $show_output,
            ''
        );

        ob_start();
        require 'modules/Administration/RebuildRelationship.php';
        $content = ob_get_clean();

        if ($input->getOption('owner')) {
            $i = new ArgvInput(array ('bin/sugarcrm', 'set:owner'));
            $cmd = new SetOwnerCommand();
            $cmd->setApplication($this->getApplication());
            $cmd->setSugar($this->getSugar());
            $cmd->run($i, $output);
        }

        if ($input->getOption('perm')) {
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
        return array(translate('LBL_ALL_MODULES'));
    }

    abstract protected function getActions();
    abstract protected function getMessage();
}
