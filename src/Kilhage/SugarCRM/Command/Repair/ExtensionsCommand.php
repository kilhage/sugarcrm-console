<?php

namespace Kilhage\SugarCRM\Command\Repair;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ExtensionsCommand extends QuickRepairAndRebuildCommand
{

    protected function configure()
    {
        parent::configure();
        $this->setName("repair:ext")
            ->setDescription("Repairs the extensions");
    }

    protected function getActions()
    {
        return array (
            "rebuildExtensions"
        );
    }

    protected function getMessage()
    {
        return "Rebuilding Extensions";
    }
}
