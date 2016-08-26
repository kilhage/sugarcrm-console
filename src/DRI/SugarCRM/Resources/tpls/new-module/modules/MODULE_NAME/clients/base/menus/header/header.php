<?php

$moduleName = '{{module_name}}';
$objectName = '{{object_name}}';
$viewdefs[$moduleName]['base']['menu']['header'] = array (
    array (
        'label' => 'LNK_NEW_RECORD',
        'acl_action' => 'create',
        'acl_module' => $moduleName,
        'icon' => 'icon-plus',
        'route' => '#' . $moduleName . '/create',
    ),
    array (
        'route' => '#' . $moduleName,
        'label' => 'LNK_VIEW_RECORDS',
        'acl_action' => 'list',
        'acl_module' => $moduleName,
        'icon' => 'icon-reorder',
    ),
);
