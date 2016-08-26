<?php

$module_name = '{{module_name}}';
$viewdefs = array (
    $module_name => array (
        'EditView' => array (
            'templateMeta' => array (
                'form' => array (
                    //'footerTpl' => '',
                    //'headerTpl' => '',
                ),
                'maxColumns' => '2',
                'widths' => array (
                    array ('label' => '10', 'field' => '30'),
                    array ('label' => '10', 'field' => '30'),
                ),
                'includes' => array (),
            ),
            'panels' => array (
                'DEFAULT' => array (
                    array ('name'),
{%if assignable or team_security %}
                    array ('{%if assignable %}assigned_user_name{% endif %}', '{%if team_security %}team_name{% endif %}'),
{% endif %}
                    array ('description'),
                ),
            ),
        ),
    ),
);
