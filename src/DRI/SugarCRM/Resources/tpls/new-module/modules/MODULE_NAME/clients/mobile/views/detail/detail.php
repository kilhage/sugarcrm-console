<?php

$module_name = '{{module_name}}';
$viewdefs[$module_name]['mobile']['view']['detail'] = array (
    'templateMeta' => array (
        'form' => array ('buttons' => array ('EDIT', 'DUPLICATE', 'DELETE',)),
        'maxColumns' => '1',
        'widths' => array (
            array ('label' => '10', 'field' => '30'),
            array ('label' => '10', 'field' => '30')
        ),
    ),
    'panels' => array (
        array (
            'label' => 'LBL_PANEL_DEFAULT',
            'fields' => array (
                'name',
{%if assignable %}
                'assigned_user_name',
{% endif %}
{%if team_security %}
                'team_name',
{% endif %}
            ),
        ),
    ),
);
