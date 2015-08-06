<?php

namespace DRI\SugarCRM\Console\Command\Repair;

/**
 * @author Emil Kilhage
 */
class ClearTplsCommand extends QuickRepairAndRebuildCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('repair:tpls')
            ->setDescription('Clears tpls');
    }

    protected function getActions()
    {
        return array(
            'clearTpls',
        );
    }

    protected function getMessage()
    {
        return 'Clearing tpls';
    }
}
