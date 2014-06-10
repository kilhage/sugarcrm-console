<?php

namespace DRI\SugarCRM\Module\Generator;

/**
 * @author Emil Kilhage
 */
class Parameters
{

    private $objectName;
    private $moduleName;
    private $tableName;
    private $importable;
    private $audited;
    private $template = 'basic';

    private $sugarObjects = array (
        'basic',
    );

    public function __construct()
    {

    }

}
