<?php

require_once dirname(__DIR__) . '/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Exception_InvalidDefinitionFormat extends VardefModifier_Exception
{

    public function __construct($message = "")
    {
        parent::__construct("Invalid Definition Formatting: '$message'");
    }

}