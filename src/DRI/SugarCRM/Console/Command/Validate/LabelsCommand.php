<?php

namespace DRI\SugarCRM\Console\Command\Validate;

use DRI\SugarCRM\Console\Command\ApplicationCommand;

/**
 * @author Emil Kilhage
 */
class LabelsCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("validate:labels")
            ->setDescription("Validates translated labels (Not implemented)");
    }

}
