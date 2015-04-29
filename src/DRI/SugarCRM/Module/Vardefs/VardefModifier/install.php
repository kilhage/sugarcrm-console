<?php

/**
 * Should be executed from cli.
 *
 * For more info:
 * $ install.php --help
 *
 * @author Emil Kilhage
 */
php_sapi_name() === 'cli' or die('Could only be executed from cli!');

require_once dirname(__FILE__).'/VardefModifier/Installer.php';

$dir = dirname(__FILE__);

while (1) {
    if (file_exists($dir.'/sugar_version.php')) {
        break;
    }
    $dir = dirname($dir);
    $dir !== '/' or die('Unable to find SugarCrm root.');
}

chdir($dir);
define('sugarEntry', true);
require_once 'include/entryPoint.php';

try {
    $installer = new VardefModifier_Installer($dir, array_slice($argv, 1));
    $installer->install();
} catch (Exception $e) {
    die($e->getMessage()."\n");
}

echo "\n* Done ! \n";
