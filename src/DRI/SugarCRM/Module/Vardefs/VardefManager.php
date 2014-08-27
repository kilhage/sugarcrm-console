<?php

namespace DRI\SugarCRM\Module\Vardefs;

/**
 * @author Emil Kilhage
 */
class VardefManager
{

    private $module;

    public function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @param $type
     *
     * @return array
     */
    public function getFieldsByType($type)
    {
        $fields = array ();

        foreach ($this->getFields() as $field) {
            if ($field["type"] == $type) {
                $fields[] = $field;
            }
        }


        return $fields;
    }

    public function getFields()
    {
        return $this->getBean()->getFieldDefinitions();
    }

    public function getBean()
    {
        return \BeanFactory::getBean($this->module);
    }

    /**
     * @param array $types
     *
     * @return array
     */
    public function getFieldsByTypes(array $types)
    {
        $fields = array ();

        foreach ($types as $type) {
            $fields = array_merge($fields, $this->getFieldsByType($type));
        }

        return $fields;
    }

}
