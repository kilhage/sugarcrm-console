<?php

function help() {
    $help = <<<TXT
$ php repair.php [[--arg(s)]] [[--<action(s)>]] [[<module(s)>]] 

Arguments:
    --help|-h: Display help text
    --auto-exec: 
    --output-html:
    --silent:

Available Actions:
    --all
    --db
    --ext
    --tpls
    --js-files
    --js-lang
    --dashlets
    --sugar-feed
    --theme
    --vardefs
    --audit-tables
    --search
    --pdf-font
TXT;

    $break = str_repeat("#", 70);
    $help = str_replace("\n", "\n# ", $help);

    echo <<<TXT
$break
# $help
$break

TXT;
    die();
}

function flag($name, $alias = null) {
    global $argv;
    if (in_array("--$name", $argv)) {
        return true;
    }

    if ($alias !== null && in_array("-$alias", $argv)) {
        return true;
    }

    return false;
}

function o($m) {
    if (!flag('silent')) {
        echo "$m";
    }
}

if (flag("help", "h")) {
    help();
}

require __DIR__ . '/_init.php';

TrackerManager::getInstance()->pause();

require_once('include/MVC/SugarApplication.php');

global $current_module, $currentModule, $moduleList, $current_user;
$currentModule = $current_module = 'Administration';
$_REQUEST['module'] = $_POST['module'] = $_GET['module'] = $current_module;
$_REQUEST['action'] = $_POST['action'] = $_GET['action'] = 'repair';

$app = new SugarApplication();
$app->startSession();
$app->controller = ControllerFactory::getController($current_module);;
$app->loadLanguages();
$app->loadGlobals();
$app->loadLicense();

$current_user = new User();
$current_user->getSystemUser();

require_once('modules/Administration/QuickRepairAndRebuild.php');
require_once 'include/utils/layout_utils.php';

$available_repair_actions = array (
    'clearAll' => array ('--all'),
    'repairDatabase' => array ('--db'),
    'rebuildExtensions' => array ('--ext'),
    'clearTpls' => array ('--tpls'),
    'clearJsFiles' => array ('--js-files'),
    'clearJsLangFiles' => array ('--js-lang'),
    'clearDashlets' => array ('--dashlets'),
    'clearSugarFeedCache' => array ('--sugar-feed'),
    'clearThemeCache' => array ('--theme'),
    'clearVardefs' => array ('--vardefs'),
    'rebuildAuditTables' => array ('--audit-tables'),
    'clearSearchCache' => array ('--search'),
    'clearPDFFontCache' => array ('--pdf-font'),
);

$actions = array ();
$modules = array ();
$autoexecute = true;
$show_output = false;

foreach ($argv as $i => $arg) {
    foreach ($available_repair_actions as $action => $available_args) {
        if (in_array($arg, $available_args)) {
            $actions[] = $action;
        }
    }

    if (in_array($arg, $moduleList)) {
        $modules[] = $arg;
    }
}

if (flag("auto-exec")) {
    $autoexecute = false;
}

if (flag("output-html")) {
    $show_output = true;
}

if (empty($actions)) {
    help();
}

if (empty($modules)) {
    $modules[] = translate('LBL_ALL_MODULES');
}

$translations = array (
    'clearAll' => 'Running Quick Repair & Rebuild',
    'repairDatabase' => 'Repairing Database',
    'rebuildExtensions' => 'Rebuilding Extensions',
    'clearTpls' => "Clearing Templates",
    'clearJsFiles' => "Clearing Javascript Files",
    'clearJsLangFiles' => "Clearing JS Lang Files",
    'clearDashlets' => "Clearing Dashlets",
    'clearSugarFeedCache' => "Clearing Sugar Feed Cache",
    'clearThemeCache' => "Clearing Theme Cache",
    'clearVardefs' => "Clearing Vardefs",
    'rebuildAuditTables' => "Rebuilding Audit Tables",
    'clearSearchCache' => "Clearing Search Cache",
    'clearPDFFontCache' => "Clearing PDF Font Cache",
);

foreach ($actions as $action) {
    if (!empty($translations[$action])) {
        o("{$translations[$action]}.. \n");
    } else {
        o("{$action}.. \n");
    }

    if (isset($available_repair_actions[$action])) {
        $repairandclear = new RepairAndClear();
        $repairandclear->repairAndClearAll(
            array ($action),
            $modules,
            $autoexecute,
            $show_output
        );
    }
}

o("Done! \n");
