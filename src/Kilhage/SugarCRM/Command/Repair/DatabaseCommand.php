<?php

namespace Kilhage\SugarCRM\Command\Repair;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class DatabaseCommand extends QuickRepairAndRebuildCommand
{

    protected function configure()
    {
        parent::configure();
        $this->setName("repair:db")
            ->setDescription("Repairs the database");
    }

    protected function getActions()
    {
        return array (
            "repairDatabase"
        );
    }

    protected function getMessage()
    {
        return "Repairing Database";
    }

}
