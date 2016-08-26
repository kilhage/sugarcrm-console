<?php

$module_name = '{{module_name}}';
$viewdefs[$module_name]['base']['layout']['detail'] = array (
    'type' => 'detail',
    'components' => array (
        array (
            'view' => 'subnavdetail',
        ),
        array (
            'view' => 'detail',
        ),
    ),
);
