<?php

require_once dirname(__DIR__).'/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Exception_InvalidFilePath extends VardefModifier_Exception
{
    public function __construct($file)
    {
        parent::__construct("Can't find file: $file");
    }
}
