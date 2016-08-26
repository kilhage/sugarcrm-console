<?php

$module_name = '{$module_name}';
$subpanel_layout = array (
    'top_buttons' => array (
        array ('widget_class' => 'SubPanelTopCreateButton'),
        array ('widget_class' => 'SubPanelTopSelectButton', 'popup_module' => $module_name),
    ),
    'where' => '',
    'list_fields' => array (
        'name' => array (
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '41%',
        ),
        'date_entered' => array (
            'vname' => 'LBL_DATE_ENTERED',
            'width' => '25%',
        ),
        'date_modified' => array (
            'vname' => 'LBL_DATE_MODIFIED',
            'width' => '25%',
        ),
        'edit_button' => array (
            'widget_class' => 'SubPanelEditButton',
            'module' => $module_name,
            'width' => '4%',
        ),
        'remove_button' => array (
            'widget_class' => 'SubPanelRemoveButton',
            'module' => $module_name,
            'width' => '5%',
        ),
    ),
);
