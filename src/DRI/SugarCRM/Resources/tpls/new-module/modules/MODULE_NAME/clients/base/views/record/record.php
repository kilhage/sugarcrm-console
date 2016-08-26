<?php

$viewdefs['{{module_name}}']['base']['view']['record'] = array (
    'panels' => array (
        array (
            'name' => 'panel_header',
            'label' => 'LBL_PANEL_HEADER',
            'header' => true,
            'fields' => array (
                array (
                    'name' => 'picture',
                    'type' => 'avatar',
                    'width' => 42,
                    'height' => 42,
                    'dismiss_label' => true,
                    'readonly' => true,
                ),
                'name',
                array (
                    'name' => 'favorite',
                    'label' => 'LBL_FAVORITE',
                    'type' => 'favorite',
                    'dismiss_label' => true,
                ),
                array (
                    'name' => 'follow',
                    'label' => 'LBL_FOLLOW',
                    'type' => 'follow',
                    'readonly' => true,
                    'dismiss_label' => true,
                ),
            )
        ),
        array (
            'name' => 'panel_body',
            'label' => 'LBL_PANEL_BODY',
            'columns' => 2,
            'labelsOnTop' => true,
            'placeholders' => true,
            'fields' => array (
{%if team_security %}
                'team_name',
{% endif %}
{%if assignable %}
                'assigned_user_name',
{% endif %}
                array (
                    'name' => 'date_modified_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_MODIFIED',
                    'fields' => array (
                        array (
                            'name' => 'date_modified',
                        ),
                        array (
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ),
                        array (
                            'name' => 'modified_by_name',
                        ),
                    ),
                ),
                array (
                    'name' => 'date_entered_by',
                    'readonly' => true,
                    'type' => 'fieldset',
                    'label' => 'LBL_DATE_ENTERED',
                    'fields' => array (
                        array (
                            'name' => 'date_entered',
                        ),
                        array (
                            'type' => 'label',
                            'default_value' => 'LBL_BY',
                        ),
                        array (
                            'name' => 'created_by_name',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
