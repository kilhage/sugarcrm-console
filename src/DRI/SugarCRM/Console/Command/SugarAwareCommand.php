<?php

namespace DRI\SugarCRM\Console\Command;

use DRI\SugarCRM\Console\Application as Sugar;

/**
 * @author Emil Kilhage
 */
interface SugarAwareCommand
{

    /**
     * @param Sugar
     */
    public function getSugar();

    /**
     * @param Sugar $sugar
     */
    public function setSugar(Sugar $sugar);

}
