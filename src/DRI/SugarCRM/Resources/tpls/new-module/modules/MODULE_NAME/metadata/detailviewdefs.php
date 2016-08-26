<?php

$module_name = '{{module_name}}';
$viewdefs = array (
    $module_name => array (
        'DetailView' => array (
            'templateMeta' => array (
                'form' => array (
                    'buttons' => array ('EDIT', 'DUPLICATE', 'DELETE'),
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
                    array (
                        array (
                            'name' => 'date_entered',
                            'customCode' => '{$fields.date_entered.value} {$APP.LBL_BY} {$fields.created_by_name.value}',
                            'label' => 'LBL_DATE_ENTERED',
                        ),
                        array (
                            'name' => 'date_modified',
                            'customCode' => '{$fields.date_modified.value} {$APP.LBL_BY} {$fields.modified_by_name.value}',
                            'label' => 'LBL_DATE_MODIFIED',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
