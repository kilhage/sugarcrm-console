<?php

namespace DRI\SugarCRM\Console\Command\Repair;

/**
 * @author Emil Kilhage
 */
class DatabaseCommand extends QuickRepairAndRebuildCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('repair:db')
            ->setDescription('Repairs the database');
    }

    protected function getActions()
    {
        return array(
            'repairDatabase',
        );
    }

    protected function getMessage()
    {
        return '<info>Repairing Database</info>';
    }
}
