<?php

$module_name = '{{module_name}}';
$listViewDefs = array (
    $module_name => array (
        'NAME' => array (
            'width' => '32',
            'label' => 'LBL_NAME',
            'default' => true,
            'link' => true,
        ),
        'DATE_ENTERED' => array (
            'width' => '32',
            'label' => 'LBL_DATE_ENTERED',
            'default' => true,
        ),
        'DATE_MODIFIED' => array (
            'width' => '32',
            'label' => 'LBL_DATE_MODIFIED',
            'default' => true,
        ),
{%if assignable %}
        'ASSIGNED_USER_NAME' => array (
            'width' => '32',
            'label' => 'LBL_ASSIGNED_USER_NAME',
            'default' => true,
            'link' => true,
        ),
{% endif %}
    ),
);
