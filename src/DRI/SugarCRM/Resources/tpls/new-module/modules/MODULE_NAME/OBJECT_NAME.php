<?php

/**
 *
 */
class {{object_name}} extends {{template_class_name}}
{

    const TABLE_NAME = '{{table_name}}';

    public $disable_row_level_security = {%if team_security == false %}true{% else %}false{% endif %};
    public $new_schema = true;
    public $module_dir = '{{module_name}}';
    public $object_name = '{{object_name}}';
    public $table_name = self::TABLE_NAME;
    public $importable = {%if importable %}true{% else %}false{% endif %};

{% for field in defined_fields %}
    public ${{field}};
{% endfor %}

    /**
     * @param string $interface
     * @return boolean
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }

        return false;
    }
}
