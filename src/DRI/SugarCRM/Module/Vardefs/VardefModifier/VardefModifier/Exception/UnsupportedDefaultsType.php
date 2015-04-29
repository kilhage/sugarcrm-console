<?php

require_once dirname(__DIR__).'/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Exception_UnsupportedDefaultsType extends VardefModifier_Exception
{
    public function __construct($type)
    {
        parent::__construct("Invalid default type: $type");
    }
}
