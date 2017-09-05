<?php

$module_name = '{{module_name}}';
$viewdefs[$module_name]['base']['view']['subpanel-list'] = array (
    'panels' =>
        array (
            array (
                'name' => 'panel_header',
                'label' => 'LBL_PANEL_1',
                'fields' =>
                    array (
                        array (
                            'label' => 'LBL_NAME',
                            'enabled' => true,
                            'default' => true,
                            'name' => 'name',
                            'link' => true,
                        ),
                        array (
                            'label' => 'LBL_DATE_MODIFIED',
                            'enabled' => true,
                            'default' => true,
                            'name' => 'date_modified',
                        ),
                    ),
            ),
        ),
    'orderBy' => array (
        'field' => 'date_modified',
        'direction' => 'desc',
    ),
);
