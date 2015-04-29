<?php

require_once dirname(__DIR__).'/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Exception_UnableToWriteCacheFile extends VardefModifier_Exception
{
    public function __construct($file)
    {
        parent::__construct("Unable to write cache file: $file");
    }
}
