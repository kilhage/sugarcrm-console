<?php

$module_name = '{{module_name}}';
$viewdefs[$module_name]['mobile']['view']['list'] = array (
    'panels' => array (
        array (
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => array (
                array (
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ),
{%if team_security %}
                array (
                    'name' => 'team_name',
                    'label' => 'LBL_TEAM',
                    'width' => 9,
                    'default' => true,
                    'enabled' => true,
                ),
{% endif %}
{%if assignable %}
                array (
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME',
                    'width' => 9,
                    'default' => true,
                    'enabled' => true,
                    'link' => true,
                ),
{% endif %}
            ),
        ),
    ),
);
