<?php

require_once dirname(__DIR__).'/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Exception_UnsupportedModule extends VardefModifier_Exception
{
    public function __construct($module_name)
    {
        parent::__construct("Unsupported module name $module_name");
    }
}
