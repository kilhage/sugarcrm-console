<?php

namespace DRI\SugarCRM\Console\Command\Repair;

/**
 * @author Emil Kilhage
 */
class RepairAllCommand extends QuickRepairAndRebuildCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('repair:all')
            ->setDescription('Repairs the extensions');
    }

    protected function getActions()
    {
        return array(
            'clearAll',
        );
    }

    protected function getMessage()
    {
        return '<info>Running Quick Repair & Rebuild</info>';
    }
}
