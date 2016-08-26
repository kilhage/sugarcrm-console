<?php

$module_name = '{{module_name}}';
$viewdefs[$module_name]['base']['view']['massupdate'] = array (
    'buttons' => array (
        array (
            'name' => 'update_button',
            'type' => 'button',
            'label' => 'Update',
            'acl_action' => 'massupdate',
            'css_class' => 'btn-primary',
            'primary' => true,
        ),
        array (
            'type' => 'button',
            'value' => 'cancel',
            'css_class' => 'btn-invisible cancel_button',
            'icon' => 'icon-remove',
            'primary' => false,
        ),
    ),
    'panels' =>
        array (
            array (
                'fields' => array ()
            )
        )
);
