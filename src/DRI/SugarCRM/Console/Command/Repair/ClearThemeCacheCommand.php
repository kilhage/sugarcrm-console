<?php

namespace DRI\SugarCRM\Console\Command\Repair;

/**
 * @author Emil Kilhage
 */
class ClearDashletsCommand extends QuickRepairAndRebuildCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('repair:theme-cache')
            ->setDescription('Clears dashlets');
    }

    protected function getActions()
    {
        return array(
            'clearDashlets',
        );
    }

    protected function getMessage()
    {
        return 'Clearing dashlets';
    }
}
