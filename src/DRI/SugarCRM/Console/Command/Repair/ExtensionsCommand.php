<?php

namespace DRI\SugarCRM\Console\Command\Repair;

/**
 * @author Emil Kilhage
 */
class ExtensionsCommand extends QuickRepairAndRebuildCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('repair:ext')
            ->setDescription('Repairs the extensions');
    }

    protected function getActions()
    {
        return array(
            'rebuildExtensions',
        );
    }

    protected function getMessage()
    {
        return 'Rebuilding Extensions';
    }
}
