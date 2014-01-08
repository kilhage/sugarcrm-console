<?php

$include_path = $_SERVER['PWD'] . '/';

while (!file_exists($include_path . 'sugar_version.php') && $include_path != '//')
{
    if (file_exists($include_path . 'docroot/sugar_version.php'))
    {
        $include_path .= 'docroot/';
    }
    else
    {
        $include_path = dirname($include_path) . '/';
    }
}

if ($include_path == '//')
{
    die("Could not find base path \n");
}

$include_path = rtrim($include_path, '/');

set_include_path($include_path);
chdir($include_path);

define('sugarEntry', true);

require_once('include/entryPoint.php');

global $current_user;
global $db;

if (empty($db))
{
    $db = DBManagerFactory::getInstance();
}

$current_user->retrieve(1);
