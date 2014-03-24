<?php

namespace Kilhage\SugarCRM\Command\Repair;

use Kilhage\SugarCRM\Command\ApplicationCommand;
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
            "auto-execute",
            "a",
            InputOption::VALUE_OPTIONAL,
            ""
        );

        $this->addOption(
            "output-html",
            "o",
            InputOption::VALUE_OPTIONAL,
            ""
        );
    }

    public function getQuickRepairAndRebuild()
    {
        require_once('modules/Administration/QuickRepairAndRebuild.php');
        require_once 'include/utils/layout_utils.php';
        $repairandclear = new \RepairAndClear();
        return $repairandclear;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getMessage());

        $auto_execute = $input->getOption("auto-execute");
        $show_output = !!$input->getOption("output-html");

        $this->getQuickRepairAndRebuild()->repairAndClearAll(
            $this->getActions(),
            $this->getActions(),
            $auto_execute,
            $show_output
        );

        $output->writeln("Done");
    }

    protected function getModules()
    {

    }

    abstract protected function getActions();
    abstract protected function getMessage();

}
