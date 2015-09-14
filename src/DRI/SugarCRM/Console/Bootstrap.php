<?php

namespace DRI\SugarCRM\Console;

/**
 * @author Emil Kilhage
 */
class Bootstrap
{
    /**
     *
     */
    public static function boot()
    {
        self::bootSugar();
        self::initDatabase();
        self::pauseTracker();
        self::disableLogging();
        self::silenceLicenseCheck();
    }

    /**
     *
     */
    public static function bootSugar()
    {
        if (!defined('sugarEntry')) {
            define('sugarEntry', true);
        }

        global $sugar_config;
        global $sugar_flavor;
        global $locale;
        global $db;
        global $beanList;
        global $beanFiles;
        global $moduleList;
        global $modInvisList;
        global $adminOnlyList;
        global $modules_exempt_from_availability_check;

        global $app_list_strings;
        global $app_strings;
        global $mod_strings;

        require_once 'include/entryPoint.php';

        // Scope is messed up due to requiring files within a function
        // We need to explicitly assign these variables to $GLOBALS
        foreach (get_defined_vars() as $key => $val) {
            $GLOBALS[$key] = $val;
        }
    }

    /**
     *
     */
    public static function silenceLicenseCheck()
    {
        $_SESSION['VALIDATION_EXPIRES_IN'] = 'valid';
    }

    /**
     *
     */
    public static function initDatabase()
    {
        $GLOBALS['db'] = \DBManagerFactory::getInstance();
    }

    /**
     *
     */
    public static function pauseTracker()
    {
        \TrackerManager::getInstance()->pause();
    }

    /**
     *
     */
    public static function disableLogging()
    {
        $GLOBALS['log'] = \LoggerManager::getLogger();
        $GLOBALS['log']->setLevel('fatal');
    }
}
