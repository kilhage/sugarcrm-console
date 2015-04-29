<?php

namespace DRI\SugarCRM\Console\Command\Module\Vardefs\VardefModifier;

if (!class_exists('Spyc')) {
    require_once dirname(__FILE__).'/spyc.php';
}

require_once dirname(__FILE__).'/VardefModifier/Exception.php';

class VardefModifier_RecursiveException extends \Exception
{
    public $table_name;

    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        parent::__construct();
    }
}

/**
 * Simplifes modifications of SugarCrm vardef definitions.
 *
 * @author Emil Kilhage
 */
class VardefModifier
{
    /**
     * Holds the default field definitions that all fields are built from
     * This is loaded from the ./defaults.yml file by VardefModifier::loadDefaults.
     *
     * @var array
     */
    private static $_defaults;

    /**
     * Modules that doesn't have the object name as dictionary key are listed here.
     *
     * @var array
     */
    private static $special_dictionary_key_mappings = array(
        'Cases' => 'Case',
    );

    /**
     * @param string $module_name
     * @param array  $dictionary
     *
     * @return VardefModifier
     */
    public static function modify($module_name, array $dictionary)
    {
        return new self($module_name, $dictionary);
    }

    /**
     * @return array
     *
     * @throws VardefModifier_Exception
     */
    private static function loadDefaults()
    {
        if (!isset(self::$_defaults)) {
            $file = dirname(__FILE__).'/defaults.yml';
            self::$_defaults = spyc_load_file($file);
        }
    }

    /**
     * Recursive helper method used by VardefModifier::remove.
     *
     * @param array $values
     * @param array $from
     */
    private static function _remove(array $values, array &$from)
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                if (isset($from[$key])) {
                    self::_remove($value, $from[$key]);
                }
            } else {
                if (isset($from[$value])) {
                    unset($from[$value]);
                }
            }
        }
    }

    /**
     * @param array $a1
     * @param array $a2
     *
     * @return array
     */
    private static function merge(array $a1, array $a2)
    {
        foreach ($a2 as $key => $value) {
            if (is_array($value)) {
                if (isset($a1[$key]) && is_array($a1[$key])) {
                    $a1[$key] = static::merge($a1[$key], $value);
                } else {
                    $a1[$key] = $value;
                }
            } else {
                $a1[$key] = $value;
            }
        }

        return $a1;
    }

    /**
     * @global array $beanList
     *
     * @param string $module_name
     *
     * @return string
     *
     * @throws VardefModifier_Exception
     */
    private static function getObjectName($module_name)
    {
        global $beanList;

        if (!isset($beanList[$module_name])) {
            require_once __DIR__.'/VardefModifier/Exception/UnsupportedModule.php';
            throw new VardefModifier_Exception_UnsupportedModule("$module_name");
        }

        return $beanList[$module_name];
    }

    /**
     * @param string $module_name
     *
     * @return string
     */
    private static function _getTableName($module_name, array $dictionary)
    {
        $object_name = self::getObjectName($module_name);

        if (!empty($dictionary[$object_name]['table'])) {
            return $dictionary[$object_name]['table'];
        } else {
            global $dictionary;
            if (!empty($dictionary[$object_name]['table'])) {
                return $dictionary[$object_name]['table'];
            } else {
                global $beanFiles;
                $bean_name = self::getObjectName($module_name);

                if (isset($beanFiles[$bean_name])) {
                    require_once $beanFiles[$bean_name];

                    if (!class_exists($bean_name)) {
                        require_once __DIR__.'/VardefModifier/Exception/UnsupportedModule.php';
                        throw new VardefModifier_Exception_UnsupportedModule($module_name);
                    }

                    $refl = new ReflectionClass($bean_name);
                    $props = $refl->getDefaultProperties();

                    if (!empty($props['table_name'])) {
                        return $props['table_name'];
                    } else {
                        require_once __DIR__.'/VardefModifier/Exception/UnsupportedModule.php';
                        throw new VardefModifier_Exception_UnsupportedModule($module_name);
                    }
                } else {
                    require_once __DIR__.'/VardefModifier/Exception/UnsupportedModule.php';
                    throw new VardefModifier_Exception_UnsupportedModule($module_name);
                }
            }
        }

        require_once __DIR__.'/VardefModifier/Exception/MissingTableName.php';
        throw new VardefModifier_Exception_MissingTableName($module_name);
    }

    /**
     * @var array
     */
    private $dictionary;

    /**
     * @var array
     */
    private $vardef;

    /**
     * @var string
     */
    private $module_name;

    /**
     * @var string
     */
    private $object_name;

    /**
     * @var string
     */
    private $table_name;

    /**
     * @param string $module_name
     * @param array  $dictionary
     */
    public function __construct($module_name, array $dictionary)
    {
        self::loadDefaults();
        $this->module_name = $module_name;
        $this->object_name = self::getObjectName($this->module_name);
        $this->dictionary = $dictionary;

        $dictionary_key = $this->getDictionaryKey();

        if (!isset($this->dictionary[$dictionary_key])) {
            $this->dictionary[$dictionary_key] = array();
        }

        $this->vardef = $this->dictionary[$dictionary_key];
        $this->defaults = self::$_defaults;
    }

    /**
     * @return string: the name of the dictionary key for the current module
     */
    public function getDictionaryKey()
    {
        if (isset(self::$special_dictionary_key_mappings[$this->module_name])) {
            return self::$special_dictionary_key_mappings[$this->module_name];
        }

        return $this->object_name;
    }

    /**
     * @param string $file
     *
     * @return \VardefModifier
     *
     * @throws VardefModifier_Exception
     */
    public function yaml($file)
    {
        if (!file_exists($file)) {
            require_once __DIR__.'/VardefModifier/Exception/InvalidFilePath.php';
            throw new VardefModifier_Exception_InvalidFilePath($file);
        }

        return $this->def(Spyc::YAMLLoad($file));
    }

    /**
     * Sets Defaults, Adds, Removes and Changes the vardef.
     *
     * Uses pretty much the whole class based on a array
     *
     * Possible keys:
     *
     *   - defaults: see VardefModifier::defaults
     *   - add:      see VardefModifier::add
     *   - change:   see VardefModifier::change
     *   - remove:   see VardefModifier::remove
     *
     * @param array $def
     *
     * @return \VardefModifier
     *
     * @throws VardefModifier_Exception
     */
    public function def(array $def)
    {
        static $keys = array('defaults', 'add', 'change', 'remove');
        // These methods needs to be executed to the correct order
        foreach ($keys as $key) {
            if (isset($def[$key])) {
                $this->$key($def[$key]);
                unset($def[$key]);
            }
        }
        if (!empty($def)) {
            require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
            throw new VardefModifier_Exception_InvalidDefinitionFormat(
                'Invalid key(s): '.implode(', ', array_keys($def))
            );
        }

        return $this;
    }

    /**
     * Adds fields, indices and relationships to the vardef.
     *
     * Possible keys:
     *
     *   - fields:        see VardefModifier::addFields
     *   - indices:       see VardefModifier::addIndices
     *   - relationships: see VardefModifier::addRelationships
     *
     * @param array $fields
     *
     * @return VardefModifier
     */
    public function add(array $keys)
    {
        foreach ($keys as $key => $fields) {
            if (!is_array($fields)) {
                require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
                throw new VardefModifier_Exception_InvalidDefinitionFormat("\$fields must be array");
            }

            switch ($key) {
                case 'fields':
                    $this->addFields($fields);
                    break;
                case 'indices':
                    $this->addIndices($fields);
                    break;
                case 'relationships':
                    $this->addRelationships($fields);
                    break;
                default:
                    require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
                    throw new VardefModifier_Exception_InvalidDefinitionFormat("$key is not supported, only fields, indices and relationships");
            }
        }

        return $this;
    }

    /**
     * Adds many indices to the vardef from a array definition.
     *
     * @todo
     *
     * @param array $indices
     *
     * @return \VardefModifier
     */
    public function addIndices(array $indices)
    {
        foreach ($indices as $fields => $settings) {
            if (is_int($fields) &&
                (is_string($settings) ||
                (is_array($settings) && isset($settings[0])))) {
                $fields = $settings;
                $settings = array();
            }

            $this->addIndex($fields, $settings);
        }

        return $this;
    }

    /**
     * Adds a index to the vardef.
     *
     * @todo
     *
     * @return \VardefModifier
     */
    public function addIndex($fields, $settings = array())
    {
        if (is_string($settings) && is_string($fields)) {
            $settings = array('type' => $settings);
        }

        if (!is_array($settings)) {
            require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
            throw new VardefModifier_Exception_InvalidDefinitionFormat("\$settings must be array");
        }

        $fields = (array) $fields;
        $default = array('fields' => $fields);
        $index = array_merge($this->getDefault('index'), $default, $settings);
        $index = array_merge(array('name' => 'idx_'.implode('_', $index['fields'])), $index);
        $this->vardef['indices'][$index['name']] = $index;

        return $this;
    }

    /**
     * Adds relationships to the vardef from a array definition.
     *
     * @param array $relationships
     *
     * @return \VardefModifier
     */
    public function addRelationships(array $relationships)
    {
        foreach ($relationships as $name => $settings) {
            if (is_int($name)) {
                $name = $settings;
                $settings = array();
            }
            $this->addRelationship($name, $settings);
        }

        return $this;
    }

    /**
     * @param array $settings
     *
     * @return \VardefModifier
     */
    private function addActivityRelationship(array $settings)
    {
        $defaults = self::merge(
            $this->getDefault('Activities'), $settings
        );
        foreach ($defaults as $module => $settings) {
            $this->addFlexRelateLink($module, $settings);
        }

        return $this;
    }

    /**
     * @param type  $module
     * @param array $settings
     */
    public function addFlexRelateLink($name, $settings = array())
    {
        if (empty($settings['module'])) {
            $settings['module'] = $name;
            $name = strtolower($name);
        }

        $settings = self::merge(
            $this->getDefault('flex_relate_link'), $settings
        );

        $module = $settings['module'];

        $table_name = self::_getTableName($module, $this->dictionary);
        $relationship_name = $this->getTableName().'_flex_relate_'.$table_name;

        $id_name = $settings['prefix'].'_id';
        $type_name = $settings['prefix'].'_type';

        $settings = self::merge($settings, array(
            'link' => array(
                'relationship' => $relationship_name,
            ),
            'relationship' => array(
                'lhs_module' => $this->module_name,
                'lhs_table' => $this->getTableName(),
                'rhs_key' => $id_name,
                'rhs_module' => $module,
                'rhs_table' => $table_name,
                'relationship_role_column_value' => $this->module_name,
                'relationship_role_column' => $type_name,
            ),
        ));

        $this->addLink($module, $settings['link']);
        $this->vardef['relationships'][$relationship_name] = $settings['relationship'];

        return $this;
    }

    /**
     * @param string $name
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    private function addFlexRelate($prefix, $settings = array())
    {
        $defaults = self::merge(
            $this->getDefault('flex_relate'), $settings
        );

        $id_name = $prefix.'_id';
        $name_name = $prefix.'_name';
        $type_name = $prefix.'_type';

        $defaults['name']['id_name'] = $id_name;
        $defaults['name']['type_name'] = $type_name;

        $this->addField($id_name, 'id', $defaults['id']);
        $this->addField($name_name, 'varchar', $defaults['name']);
        $this->addField($type_name, 'varchar', $defaults['type']);
        $this->addIndex($id_name);

        return $this;
    }

    /**
     * Adds a relationship to the vardef.
     *
     * @param string       $name:     name of the relation or the module name
     * @param string|array $settings: module name or relationship settings
     *
     * @return \VardefModifier
     */
    public function addRelationship($name, $settings = array())
    {
        switch ($name) {
            case 'Activities':
                return $this->addActivityRelationship($settings);
        }

        $relationship_names = array($this->object_name);
        if (is_string($settings)) {
            $settings = array('module' => $settings);
            $relationship_names[] = $name;
        } elseif (empty($settings['module'])) {
            $settings['module'] = $name;
            $name = strtolower(self::getObjectName($settings['module']));
        } else {
            $relationship_names[] = $name;
        }

        switch ($settings['module']) {
            case 'Contacts':
                $settings = self::merge(array(
                    'name' => array(
                        'rname' => 'name',
                        'db_concat_fields' => array('first_name', 'last_name'),
                    ),
                ), $settings);
        }

        $relationship_names[] = $settings['module'];
        $relationship_name = strtolower(implode('_', $relationship_names));

        $vname = isset($settings['vname']) ? $settings['vname'] : $this->getVName($name);
        $rhs_key = $name.'_id';

        $_settings = static::merge($this->getDefault('relationship'), array(
            'id' => array(
                'name' => $rhs_key,
                'vname' => $vname,
            ),
            'name' => array(
                'name' => $name.'_name',
                'vname' => $vname,
                'module' => $settings['module'],
            ),
            'link' => array(
                'name' => $name.'_link',
                'vname' => $vname,
                'module' => $settings['module'],
            ),
            'index' => array(),
            'relationship' => array(
                'lhs_module' => $settings['module'],
                'lhs_table' => self::_getTableName($settings['module'], $this->dictionary),
                'rhs_module' => $this->module_name,
                'rhs_table' => $this->getTableName(),
                'rhs_key' => $rhs_key,
                'name' => $relationship_name,
            ),
        ));

        // Set the name field to required if set in the root
        if (isset($settings['required'])) {
            $_settings['name']['required'] = $settings['required'];
        }

        $_settings = static::merge($_settings, $settings);

        // Make sure that the id field name are synced
        $_settings['name']['id_name'] = $_settings['id']['name'];
        $_settings['relationship']['rhs_key'] = $_settings['id']['name'];

        // Make sure that the link field names are synced
        $_settings['name']['link'] = $_settings['link']['name'];

        // Make sure that the relationship names are synced
        $relationship_name = $_settings['relationship']['name'];
        $_settings['link']['relationship'] = $relationship_name;
        unset($_settings['relationship']['name']);

        // Add the built releationship
        $this->vardef['relationships'][$relationship_name] = $_settings['relationship'];

        // Add the fields
        foreach (array('id', 'name', 'link') as $type) {
            $this->addField($_settings[$type]['name'], $type, $_settings[$type]);
        }

        // Add the index if not set to non-array value
        if (isset($_settings['index']) && is_array($_settings['index'])) {
            $this->addIndex($_settings['id']['name'], $_settings['index']);
        }

        return $this;
    }

    /**
     * Makes changes to the vardefs.
     *
     * @param array $change
     *
     * @return \VardefModifier
     */
    public function change(array $changes)
    {
        $this->vardef = self::merge($this->vardef, $changes);

        return $this;
    }

    /**
     * Removes fields / properties this the vardef.
     *
     * @param array $keys
     *
     * @return \VardefModifier
     */
    public function remove(array $values)
    {
        static::_remove($values, $this->vardef);

        return $this;
    }

    /**
     * Changes the default field properties.
     *
     * @param array $field_defaults
     *
     * @return \VardefModifier
     *
     * @throws VardefModifier_Exception
     */
    public function defaults(array $field_defaults)
    {
        foreach ($field_defaults as $name => $field_default) {
            $this->setDefault($name, $field_default);
        }

        return $this;
    }

    /**
     * Adds fields based on a array definition.
     *
     * The keys in the array should be the field type
     * and the value an array of fields and definitions
     *
     * See VardefModifier::addField for supported field types
     *
     * @param array $fields
     *
     * @return \VardefModifier
     */
    public function addFields(array $types)
    {
        foreach ($types as $type => $fields) {
            if (is_array($fields) && is_string($type)) {
                foreach ($fields as $name => $settings) {
                    if (is_int($name)) {
                        $name = $settings;
                        $this->addField($name, $type);
                    } else {
                        $this->addField($name, $type, $settings);
                    }
                }
            } else {
                require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
                throw new VardefModifier_Exception_InvalidDefinitionFormat('Not Implemented');
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $type
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    public function addField($name, $type, array $settings = array())
    {
        if (!is_string($name) || empty($name)) {
            require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
            throw new VardefModifier_Exception_InvalidDefinitionFormat('Invalid type of name');
        }
        switch ($type) {
            case 'enum':
                $this->addEnum($name, $settings);
                break;
            case 'multienum':
                $this->addMultienum($name, $settings);
                break;
            case 'link':
                $this->addLink($name, $settings);
                break;
            case 'currency':
                $this->addCurrency($name, $settings);
                break;
            case 'name':
                $this->addName($name, $settings);
                break;
            case 'relate':
                $this->addRelate($name, $settings);
                break;
            case 'address':
                $this->addAddress($name, $settings);
                break;
            case 'flex_relate_link':
                $this->addFlexRelateLink($name, $settings);
                break;
            case 'flex_relate':
                $this->addFlexRelate($name, $settings);
                break;
            default:
                if ($this->hasDefault($type)) {
                    $this->addDefaultField($name, $this->getDefault($type), $settings);
                } else {
                    require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
                    throw new VardefModifier_Exception_InvalidDefinitionFormat("Invalid field type: '$type'");
                }
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     * @return \VardefModifier
     */
    public function hasField($name)
    {
        return isset($this->vardef['fields'][$name]);
    }

    /**
     * @return \VardefModifier
     */
    public function addCurrencyRelation()
    {
        if (!$this->hasField('currency_id')) {
            $this->addRelationship('Currencies', array(
                'id' => array(
                    'type' => 'currency_id',
                    'dbType' => 'id',
                    'group' => 'currency_id',
                    'default' => '-99',
                    'function' => array(
                        'name' => 'getCurrencyDropDown',
                        'returns' => 'html',
                    ),
                ),
                'name' => array(
                    'function' => array(
                        'name' => 'getCurrencyNameDropDown',
                        'returns' => 'html',
                    ),
                ),
            ));
            $this->addRelate('currency_symbol', array(
                'module' => 'Currencies',
                'rname' => 'symbol',
                'function' => array(
                    'name' => 'getCurrencySymbolDropDown',
                    'returns' => 'html',
                ),
            ));
        }

        return $this;
    }

    /**
     * @return array
     */
    public function get()
    {
        $this->dictionary[$this->getDictionaryKey()] = $this->vardef;

        return $this->dictionary;
    }

    /**
     * @return string
     */
    private function getTableName()
    {
        $this->table_name = self::_getTableName($this->module_name, $this->dictionary);

        if (empty($this->table_name)) {
            require_once __DIR__.'/VardefModifier/Exception/MissingTableName.php';
            throw new VardefModifier_Exception_MissingTableName($this->module_name);
        }

        return $this->table_name;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private function getDefault($type)
    {
        if (!isset($this->defaults[$type])) {
            require_once __DIR__.'/VardefModifier/Exception/UnsupportedDefaultsType.php';
            throw new VardefModifier_Exception_UnsupportedDefaultsType($type);
        }

        return $this->defaults[$type];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function hasDefault($type)
    {
        return isset($this->defaults[$type]);
    }

    /**
     * @param string $name
     * @param array  $field_default
     *
     * @throws VardefModifier_Exception
     */
    private function setDefault($name, array $field_default)
    {
        if (!isset($this->defaults[$name])) {
            require_once __DIR__.'/VardefModifier/Exception/UnsupportedDefaultsType.php';
            throw new VardefModifier_Exception_UnsupportedDefaultsType($name);
        }

        $this->defaults[$name] = array_merge($this->defaults[$name], $field_default);
    }

    /**
     * @param string $name
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    private function addName($name, array $settings)
    {
        return $this->addRelate($name, array_merge(
            $this->getDefault('name'), $settings
        ));
    }

    /**
     * @param string $name
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    private function addRelate($name, array $settings)
    {
        if (!isset($settings['module'])) {
            require_once __DIR__.'/VardefModifier/Exception/InvalidDefinitionFormat.php';
            throw new VardefModifier_Exception_InvalidDefinitionFormat('Missing module');
        }

        $default = array(
            'rname' => $name,
            'table' => self::_getTableName($settings['module'], $this->dictionary),
            'id_name' => strtolower(self::getObjectName($settings['module'])).'_id',
        );

        return $this->addDefaultField(
            $name, $this->getDefault('relate'), $default, $settings
        );
    }

    /**
     * @param string $name
     * @param array  $settings
     */
    private function addAddress($name, array $settings = array())
    {
        $defaults = self::merge($this->getDefault('address'), $settings);
        $all = $defaults['all'];
        unset($defaults['all']);
        if (empty($all['group'])) {
            $all['group'] = $name.'_address';
        }
        foreach ($defaults as $field_name => $field_settings) {
            if (is_array($field_settings)) {
                $field_settings = self::merge($all, $field_settings);
                $this->addField(
                    $name.'_address_'.$field_name, $field_settings['type'], $field_settings
                );
            }
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array
     *
     * @return \VardefModifier
     */
    private function addCurrency($name, array $settings = array())
    {
        $template = $this->getDefault('currency');

        return $this->
                addCurrencyRelation()->
                addDefaultField($name, $template, $settings)->
                addDefaultField($name.'_usdollar', $template, array('group' => $name), $settings);
    }

    /**
     * Adds a link field.
     *
     * @param string $name
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    private function addLink($name, array $settings = array())
    {
        if (empty($settings['module'])) {
            $settings['module'] = $name;
            $name = strtolower($name);
        }

        $object_name = self::getObjectName($settings['module']);
        $relationship_names = array($object_name);

        if (!empty($settings['relationship_name'])) {
            $relationship_names[] = $settings['relationship_name'];
            // No need to store this in the vardef...
            unset($settings['relationship_name']);
        }

        $relationship_names[] = $this->module_name;

        $this->addFieldToVardef($name, array_merge(
            $this->getDefault('link'), array(
                'bean_name' => $object_name,
                'relationship' => strtolower(implode('_', $relationship_names)),
            ), $settings
        ));

        return $this;
    }

    /**
     * @param string $name
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    private function addEnum($name, array $settings = array())
    {
        return $this->addEnumLike($name, $this->getDefault('enum'), $settings);
    }

    /**
     * @param string $name
     * @param array  $settings
     *
     * @return \VardefModifier
     */
    private function addMultienum($name, array $settings = array())
    {
        return $this->addEnumLike($name, $this->getDefault('multienum'), $settings);
    }

    /**
     * @param string $name
     * @param array  $settings
     * @param array  $default
     *
     * @return \VardefModifier
     */
    private function addEnumLike($name, array $default, array $settings)
    {
        return $this->addDefaultField(
            $name, array('options' => strtolower($this->module_name.'_'.$name).'_list'), $default, $settings
        );
    }

    /**
     * @param string $name
     *
     * @return \VardefModifier
     */
    private function addDefaultField($name)
    {
        $args = func_get_args();
        $args[0] = $this->getBase();
        $this->addFieldToVardef($name, call_user_func_array(
            'array_merge', $args
        ));

        return $this;
    }

    /**
     * @return array
     */
    private function getBase()
    {
        return $this->getDefault('_base');
    }

    /**
     * @param string $name
     *
     * @return array
     */
    private function addFieldToVardef($name, array $definition)
    {
        $this->vardef['fields'][$name] = array_merge(array(
            'name' => $name,
            'vname' => $this->getVName($name),
        ), $definition);
    }

    /**
     * @return string
     */
    private function getVName($name)
    {
        return 'LBL_'.strtoupper($name);
    }
}
