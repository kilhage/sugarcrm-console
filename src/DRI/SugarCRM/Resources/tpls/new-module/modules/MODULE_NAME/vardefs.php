<?php

$dictionary['{{object_name}}'] = array (
    'table' => '{{table_name}}',
    'audited' => {% if audited %}true{% else %}false{% endif %},
    'unified_search' => true,
    'duplicate_merge' => true,
    'activity_enabled' => false,
    'comment' => '{{object_name}}',
    'fields' => array (),
    'relationships' => array (),
    'optimistic_lock' => true,
);

VardefManager::createVardef('{{module_name}}', '{{object_name}}', array (
{% for template_name in templates %}
    '{{template_name}}',
{% endfor %}
));
