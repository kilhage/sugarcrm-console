<?php

require_once dirname(__DIR__).'/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Exception_MissingTableName extends VardefModifier_Exception
{
    public function __construct($module_name)
    {
        parent::__construct("Missing table name for module $module_name");
    }
}
