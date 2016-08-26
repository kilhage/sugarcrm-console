<?php

$module_name = '{{module_name}}';
$metafiles[$module_name] = array (
    'detailviewdefs' => get_custom_file_if_exists('modules/' . $module_name . '/metadata/detailviewdefs.php'),
    'editviewdefs' => get_custom_file_if_exists('modules/' . $module_name . '/metadata/editviewdefs.php'),
    'listviewdefs' => get_custom_file_if_exists('modules/' . $module_name . '/metadata/listviewdefs.php'),
    'searchdefs' => get_custom_file_if_exists('modules/' . $module_name . '/metadata/searchdefs.php'),
    'popupdefs' => get_custom_file_if_exists('modules/' . $module_name . '/metadata/popupdefs.php'),
    'searchfields' => get_custom_file_if_exists('modules/' . $module_name . '/metadata/SearchFields.php'),
);
