<?php

namespace Kilhage\SugarCRM\Command;

use Kilhage\SugarCRM\Application as Sugar;

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
