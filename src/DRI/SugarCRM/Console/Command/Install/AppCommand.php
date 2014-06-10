<?php

namespace DRI\SugarCRM\Console\Command\Install;

use DRI\SugarCRM\Console\Command\ApplicationCommand;

/**
 * @author Emil Kilhage
 */
class AppCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("install:app")
            ->setDescription("Installs the SugarCRM application (Not implemented)");
    }

}
