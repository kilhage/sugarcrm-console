<?php

require_once dirname(dirname(__FILE__)).'/VardefModifier.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Test extends PHPUnit_Framework_TestCase
{
    private $module_name;
    private $object_name;

    protected function setUp()
    {
        global $beanList, $dictionary;

        if (!isset($beanList['Accounts'])) {
            $beanList['Accounts'] = 'Account';
        }

        if (!isset($beanList['Currencies'])) {
            $beanList['Currencies'] = 'Currency';
        }

        if (!isset($beanList['Contacts'])) {
            $beanList['Contacts'] = 'Contact';
        }

        if (!isset($beanList['Cases'])) {
            $beanList['Cases'] = 'aCase';
        }

        if (!isset($beanList['Tasks'])) {
            $beanList['Tasks'] = 'Task';
        }

        if (!isset($beanList['ProductTypes'])) {
            $beanList['ProductTypes'] = 'ProductType';
        }

        if (!isset($beanList['Notes'])) {
            $beanList['Notes'] = 'Note';
        }

        if (!isset($beanList['Meetings'])) {
            $beanList['Meetings'] = 'Meeting';
        }

        if (!isset($beanList['Calls'])) {
            $beanList['Calls'] = 'Call';
        }

        if (!isset($beanList['Emails'])) {
            $beanList['Emails'] = 'Email';
        }

        $this->module_name = '_MyModules';
        $this->object_name = '_MyModule';
        $beanList[$this->module_name] = $this->object_name;
        $dictionary[$this->object_name] = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(),
            'relationships' => array(),
        );
    }

    protected function tearDown()
    {
        global $beanList, $dictionary;
        unset($beanList[$this->module_name]);
        unset($dictionary[$this->object_name]);
    }

    /**
     * @global type $dictionary
     *
     * @return \VardefModifier
     */
    private function create()
    {
        global $dictionary;

        return VardefModifier::modify($this->module_name, $dictionary);
    }

    public function test_Varchar()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'varchar',
                    'len' => '255',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'varchar');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'varchar' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $real_dic['fields']['field1']['len'] = '20';
        $m = $this->create();
        $m->addField('field1', 'varchar', array(
            'len' => '20',
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $real_dic['fields']['field1']['len'] = '30';
        $real_dic['fields']['field1']['audited'] = false;
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'varchar' => array(
                    'field1' => array(
                        'len' => '30',
                        'audited' => false,
                    ),
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_Bool()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'bool',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'bool');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'bool' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_Text()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'text',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'text');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'text' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_Date()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'date',
                    'enable_range_search' => true,
                    'options' => 'date_range_search_dom',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'date');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'date' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_Decimal()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'decimal',
                    'len' => '26,6',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'decimal');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'decimal' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_Image()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'image',
                    'dbType' => 'varchar',
                    'height' => '100',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'image');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'image' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_DateTimeCombo()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'datetimecombo',
                    'dbType' => 'datetime',
                    'enable_range_search' => true,
                    'options' => 'date_range_search_dom',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'datetimecombo');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'datetimecombo' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_Address()
    {
        $rd = array(
            '_MyModule' => array(
                'favorites' => true,
                'fields' => array(
                    'primary_address_street' => array(
                        'name' => 'primary_address_street',
                        'vname' => 'LBL_PRIMARY_ADDRESS_STREET',
                        'required' => false,
                        'reportable' => true,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'varchar',
                        'len' => 150,
                        'merge_filter' => 'enabled',
                        'group' => 'primary_address',
                    ),
                    'primary_address_city' => array(
                        'name' => 'primary_address_city',
                        'vname' => 'LBL_PRIMARY_ADDRESS_CITY',
                        'required' => false,
                        'reportable' => true,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'varchar',
                        'len' => 100,
                        'merge_filter' => 'enabled',
                        'group' => 'primary_address',
                    ),
                    'primary_address_state' => array(
                        'name' => 'primary_address_state',
                        'vname' => 'LBL_PRIMARY_ADDRESS_STATE',
                        'required' => false,
                        'reportable' => true,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'varchar',
                        'len' => 100,
                        'merge_filter' => 'enabled',
                        'group' => 'primary_address',
                    ),
                    'primary_address_postalcode' => array(
                        'name' => 'primary_address_postalcode',
                        'vname' => 'LBL_PRIMARY_ADDRESS_POSTALCODE',
                        'required' => false,
                        'reportable' => true,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'varchar',
                        'len' => 20,
                        'merge_filter' => 'enabled',
                        'group' => 'primary_address',
                    ),
                    'primary_address_country' => array(
                        'name' => 'primary_address_country',
                        'vname' => 'LBL_PRIMARY_ADDRESS_COUNTRY',
                        'required' => false,
                        'reportable' => true,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'varchar',
                        'len' => 255,
                        'merge_filter' => 'enabled',
                        'group' => 'primary_address',
                    ),
                ),
                'indices' => array(),
                'relationships' => array(),
            ),
        );
        $dic = $this->create()
            ->addField('primary', 'address')
            ->get();
        $this->assertEquals($rd, $dic);
    }

    public function test_setDefault()
    {
        $rd = array(
            '_MyModule' => array(
                'favorites' => true,
                'fields' => array(
                    'my' => array(
                        'name' => 'my',
                        'vname' => 'LBL_MY',
                        'required' => false,
                        'reportable' => true,
                        'audited' => true,
                        'importable' => 'true',
                        'massupdate' => false,
                        'type' => 'varchar',
                        'len' => 4,
                    ),
                ),
                'indices' => array(),
                'relationships' => array(),
            ),
        );
        $dic = $this->create()
            ->defaults(array(
                'varchar' => array('len' => 4),
            ))
            ->addField('my', 'varchar')
            ->get();
        $this->assertEquals($rd, $dic);
    }

    public function test_addActivityRelation()
    {
        $rd = array(
            '_MyModule' => array(
                'favorites' => true,
                'fields' => array(
                    'tasks' => array(
                        'name' => 'tasks',
                        'vname' => 'LBL_TASKS',
                        'source' => 'non-db',
                        'type' => 'link',
                        'bean_name' => 'Task',
                        'relationship' => '_mymodules_activities_tasks',
                        'module' => 'Tasks',
                    ),
                    'notes' => array(
                        'name' => 'notes',
                        'vname' => 'LBL_NOTES',
                        'source' => 'non-db',
                        'type' => 'link',
                        'bean_name' => 'Note',
                        'relationship' => '_mymodules_activities_notes',
                        'module' => 'Notes',
                    ),
                    'meetings' => array(
                        'name' => 'meetings',
                        'vname' => 'LBL_MEETINGS',
                        'source' => 'non-db',
                        'type' => 'link',
                        'bean_name' => 'Meeting',
                        'relationship' => '_mymodules_activities_meetings',
                        'module' => 'Meetings',
                    ),
                    'calls' => array(
                        'name' => 'calls',
                        'vname' => 'LBL_CALLS',
                        'source' => 'non-db',
                        'type' => 'link',
                        'bean_name' => 'Call',
                        'relationship' => '_mymodules_activities_calls',
                        'module' => 'Calls',
                    ),
                    'emails' => array(
                        'name' => 'emails',
                        'vname' => 'LBL_EMAILS',
                        'source' => 'non-db',
                        'type' => 'link',
                        'bean_name' => 'Email',
                        'relationship' => '_mymodules_activities_emails',
                        'module' => 'Emails',
                    ),
                ),
                'indices' => array(),
                'relationships' => array(
                    '_mymodules_activities_tasks' => array(
                        'lhs_key' => 'id',
                        'rhs_key' => 'parent_id',
                        'relationship_type' => 'one-to-many',
                        'relationship_role_column' => 'parent_type',
                        'lhs_module' => '_MyModules',
                        'lhs_table' => '_mymodules',
                        'rhs_module' => 'Tasks',
                        'rhs_table' => 'tasks',
                        'relationship_role_column_value' => '_MyModules',
                    ),
                    '_mymodules_activities_notes' => array(
                        'lhs_key' => 'id',
                        'rhs_key' => 'parent_id',
                        'relationship_type' => 'one-to-many',
                        'relationship_role_column' => 'parent_type',
                        'lhs_module' => '_MyModules',
                        'lhs_table' => '_mymodules',
                        'rhs_module' => 'Notes',
                        'rhs_table' => 'notes',
                        'relationship_role_column_value' => '_MyModules',
                    ),
                    '_mymodules_activities_meetings' => array(
                        'lhs_key' => 'id',
                        'rhs_key' => 'parent_id',
                        'relationship_type' => 'one-to-many',
                        'relationship_role_column' => 'parent_type',
                        'lhs_module' => '_MyModules',
                        'lhs_table' => '_mymodules',
                        'rhs_module' => 'Meetings',
                        'rhs_table' => 'meetings',
                        'relationship_role_column_value' => '_MyModules',
                    ),
                    '_mymodules_activities_calls' => array(
                        'lhs_key' => 'id',
                        'rhs_key' => 'parent_id',
                        'relationship_type' => 'one-to-many',
                        'relationship_role_column' => 'parent_type',
                        'lhs_module' => '_MyModules',
                        'lhs_table' => '_mymodules',
                        'rhs_module' => 'Calls',
                        'rhs_table' => 'calls',
                        'relationship_role_column_value' => '_MyModules',
                    ),
                    '_mymodules_activities_emails' => array(
                        'lhs_key' => 'id',
                        'rhs_key' => 'parent_id',
                        'relationship_type' => 'one-to-many',
                        'relationship_role_column' => 'parent_type',
                        'lhs_module' => '_MyModules',
                        'lhs_table' => '_mymodules',
                        'rhs_module' => 'Emails',
                        'rhs_table' => 'emails',
                        'relationship_role_column_value' => '_MyModules',
                    ),
                ),
            ),
        );
        $dic = $this->create()
            ->addRelationships(array(
                'Activities',
            ))
            ->get();
        $this->assertEquals($rd, $dic);
    }

    public function test_Url()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'url',
                    'dbType' => 'varchar',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'url');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'url' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_datetime()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'datetime',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'datetime');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'datetime' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_float()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'float',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'float');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'float' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_phone()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'phone',
                    'dbType' => 'varchar',
                    'len' => 100,
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'phone');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'phone' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_id()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'id',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'id');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'id' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_currency()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'currency_id' => array(
                    'name' => 'currency_id',
                    'vname' => 'LBL_CURRENCY',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'id',
                    'group' => 'currency_id',
                    'function' => array(
                        'name' => 'getCurrencyDropDown',
                        'returns' => 'html',
                    ),
                ),
                'currency_name' => array(
                    'name' => 'currency_name',
                    'vname' => 'LBL_CURRENCY',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'module' => 'Currencies',
                    'rname' => 'name',
                    'function' => array(
                        'name' => 'getCurrencyNameDropDown',
                        'returns' => 'html',
                    ),
                    'table' => 'currencies',
                    'id_name' => 'currency_id',
                    'source' => 'non-db',
                    'type' => 'relate',
                    'link' => 'currency_link',
                ),
                'currency_symbol' => array(
                    'name' => 'currency_symbol',
                    'vname' => 'LBL_CURRENCY_SYMBOL',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'module' => 'Currencies',
                    'rname' => 'symbol',
                    'function' => array(
                        'name' => 'getCurrencySymbolDropDown',
                        'returns' => 'html',
                    ),
                    'table' => 'currencies',
                    'id_name' => 'currency_id',
                    'source' => 'non-db',
                    'type' => 'relate',
                ),
                'currency_link' => array(
                    'name' => 'currency_link',
                    'vname' => 'LBL_CURRENCY',
                    'source' => 'non-db',
                    'type' => 'link',
                    'bean_name' => 'Currency',
                    'module' => 'Currencies',
                    'relationship' => '_mymodule_currencies',
                ),
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'currency',
                    'dbType' => 'double',
                ),
                'field1_usdollar' => array(
                    'name' => 'field1_usdollar',
                    'vname' => 'LBL_FIELD1_USDOLLAR',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'currency',
                    'dbType' => 'double',
                    'group' => 'field1',
                ),
            ),
            'indices' => array(
                'idx__mymodules_currency_id' => array(
                    'type' => 'index',
                    'name' => 'idx__mymodules_currency_id',
                    'fields' => array('currency_id'),
                ),
            ),
            'relationships' => array(
                '_mymodule_currencies' => array(
                    'relationship_type' => 'one-to-many',
                    'lhs_key' => 'id',
                    'lhs_module' => 'Currencies',
                    'lhs_table' => 'currencies',
                    'rhs_module' => '_MyModules',
                    'rhs_table' => '_mymodules',
                    'rhs_key' => 'currency_id',
                ),
            ),
        );
        $m = $this->create();
        $m->addField('field1', 'currency');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'currency' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $real_dic['fields']['field1']['len'] = '20,1';
        $real_dic['fields']['field1_usdollar']['len'] = '20,1';
        $m->add(array(
            'fields' => array(
                'currency' => array(
                    'field1' => array(
                        'len' => '20,1',
                    ),
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_enum()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'enum',
                    'options' => '_mymodules_field1_list',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'enum');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'enum' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_multienum()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'multienum',
                    'options' => '_mymodules_field1_list',
                    'isMultiSelect' => true,
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'multienum');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'multienum' => array(
                    'field1',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $real_dic['fields']['field1']['isMultiSelect'] = false;
        $m->add(array(
            'fields' => array(
                'multienum' => array(
                    'field1' => array(
                        'isMultiSelect' => false,
                    ),
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_relate()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'field1' => array(
                    'name' => 'field1',
                    'vname' => 'LBL_FIELD1',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'rname' => 'field1',
                    'table' => 'accounts',
                    'id_name' => 'account_id',
                    'source' => 'non-db',
                    'type' => 'relate',
                    'module' => 'Accounts',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('field1', 'relate', array('module' => 'Accounts'));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $m = $this->create();
        $m->add(array(
            'fields' => array(
                'relate' => array(
                    'field1' => array(
                        'module' => 'Accounts',
                    ),
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
        $real_dic['fields']['field1']['rname'] = 'field2';
        $m->add(array(
            'fields' => array(
                'relate' => array(
                    'field1' => array(
                        'module' => 'Accounts',
                        'rname' => 'field2',
                    ),
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_addLinkSimple()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'accounts' => array(
                    'name' => 'accounts',
                    'vname' => 'LBL_ACCOUNTS',
                    'source' => 'non-db',
                    'type' => 'link',
                    'bean_name' => 'Account',
                    'module' => 'Accounts',
                    'relationship' => 'account__mymodules',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('Accounts', 'link');
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_addLinkNamed()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'cases' => array(
                    'name' => 'cases',
                    'vname' => 'LBL_CASES',
                    'source' => 'non-db',
                    'type' => 'link',
                    'bean_name' => 'aCase',
                    'module' => 'Cases',
                    'relationship' => 'acase_case__mymodules',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('cases', 'link', array(
            'module' => 'Cases',
            'relationship_name' => 'case',
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_addLinkNamed2()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'contact_persons' => array(
                    'name' => 'contact_persons',
                    'vname' => 'LBL_CONTACT_PERSONS',
                    'source' => 'non-db',
                    'type' => 'link',
                    'bean_name' => 'Contact',
                    'module' => 'Contacts',
                    'relationship' => 'contact_contact_person_for__mymodules',
                ),
            ),
            'indices' => array(),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addField('contact_persons', 'link', array(
            'module' => 'Contacts',
            'relationship_name' => 'contact_person_for',
        ));
        $dic = $m->get();
        $this->assertEquals($real_dic, $dic[$this->object_name]);
    }

    public function test_addIndex()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(
                'idx_'.strtolower($this->module_name).'_name' => array(
                    'type' => 'index',
                    'name' => 'idx_'.strtolower($this->module_name).'_name',
                    'fields' => array('name'),
                ),
            ),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addIndices(array(
            'name',
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addIndex2()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(
                'idx_'.strtolower($this->module_name).'_name' => array(
                    'type' => 'unique',
                    'name' => 'idx_'.strtolower($this->module_name).'_name',
                    'fields' => array('name'),
                ),
            ),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addIndices(array(
            'name' => array('type' => 'unique'),
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addIndex3()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(
                'idx_'.strtolower($this->module_name).'_name_deleted' => array(
                    'type' => 'index',
                    'name' => 'idx_'.strtolower($this->module_name).'_name_deleted',
                    'fields' => array('name', 'deleted'),
                ),
            ),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addIndices(array(
            array('name', 'deleted'),
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addIndex4()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(
                'idx_index_name' => array(
                    'type' => 'index',
                    'name' => 'idx_index_name',
                    'fields' => array('name', 'deleted'),
                ),
            ),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addIndices(array(
            array('fields' => array('name', 'deleted'), 'name' => 'idx_index_name'),
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addIndex5()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(
                'idx_index_name' => array(
                    'type' => 'index',
                    'name' => 'idx_index_name',
                    'fields' => array('name', 'deleted'),
                ),
            ),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addIndices(array(
            'name' => array('fields' => array('name', 'deleted'), 'name' => 'idx_index_name'),
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addIndex6()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(),
            'indices' => array(
                'idx_'.strtolower($this->module_name).'_name' => array(
                    'type' => 'unique',
                    'name' => 'idx_'.strtolower($this->module_name).'_name',
                    'fields' => array('name'),
                ),
            ),
            'relationships' => array(),
        );
        $m = $this->create();
        $m->addIndices(array(
            'name' => 'unique',
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_change()
    {
        $m = $this->create();
        $m->addField('test_change', 'int', array('len' => '2'));
        $dic = $m->get();
        $this->assertEquals('2', $dic[$this->object_name]['fields']['test_change']['len']);
        $m->change(array(
            'fields' => array(
                'test_change' => array(
                    'len' => '3',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertEquals('3', $dic[$this->object_name]['fields']['test_change']['len']);
    }

    public function test_remove()
    {
        $m = $this->create();
        $m->addField('test_remove', 'int');
        $dic = $m->get();
        $this->assertTrue(isset($dic[$this->object_name]['fields']['test_remove']));
        $this->assertTrue(isset($dic[$this->object_name]['fields']['test_remove']['len']));
        $m->remove(array(
            'fields' => array(
                'test_remove' => array(
                    'len',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']['len']));
    }

    public function test_remove2()
    {
        $m = $this->create();
        $m->addField('test_remove', 'int');
        $dic = $m->get();
        $this->assertTrue(isset($dic[$this->object_name]['fields']['test_remove']));
        $this->assertTrue(isset($dic[$this->object_name]['fields']['test_remove']['len']));
        $m->remove(array(
            'fields' => array(
                'test_remove',
            ),
        ));
        $dic = $m->get();
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']));
    }

    public function test_remove3()
    {
        $m = $this->create();
        $dic = $m->get();
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']));
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']['len']));
        $m->remove(array(
            'fields' => array(
                'test_remove',
            ),
        ));
        $dic = $m->get();
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']));
    }

    public function test_remove4()
    {
        $m = $this->create();
        $dic = $m->get();
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']));
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']['len']));
        $m->remove(array(
            'fields' => array(
                'test_remove' => array(
                    'len',
                ),
            ),
        ));
        $dic = $m->get();
        $this->assertTrue(!isset($dic[$this->object_name]['fields']['test_remove']['len']));
    }

    public function test_addRelationship()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'account_id' => array(
                    'name' => 'account_id',
                    'vname' => 'LBL_ACCOUNT',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'id',
                ),
                'account_name' => array(
                    'name' => 'account_name',
                    'vname' => 'LBL_ACCOUNT',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'source' => 'non-db',
                    'type' => 'relate',
                    'rname' => 'name',
                    'table' => 'accounts',
                    'id_name' => 'account_id',
                    'module' => 'Accounts',
                    'link' => 'account_link',
                ),
                'account_link' => array(
                    'name' => 'account_link',
                    'vname' => 'LBL_ACCOUNT',
                    'source' => 'non-db',
                    'type' => 'link',
                    'bean_name' => 'Account',
                    'relationship' => '_mymodule_accounts',
                    'module' => 'Accounts',
                ),
            ),
            'indices' => array(
                'idx__mymodules_account_id' => array(
                    'type' => 'index',
                    'name' => 'idx__mymodules_account_id',
                    'fields' => array('account_id'),
                ),
            ),
            'relationships' => array(
                '_mymodule_accounts' => array(
                    'relationship_type' => 'one-to-many',
                    'lhs_key' => 'id',
                    'lhs_module' => 'Accounts',
                    'lhs_table' => 'accounts',
                    'rhs_module' => '_MyModules',
                    'rhs_table' => '_mymodules',
                    'rhs_key' => 'account_id',
                ),
            ),
        );
        $m = $this->create();
        $m->addRelationships(array(
            'Accounts',
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addRelationshipNamedSimple()
    {
        $real_dic = array(
            'favorites' => true,
            'fields' => array(
                'store_id' => array(
                    'name' => 'store_id',
                    'vname' => 'LBL_STORE',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'type' => 'id',
                ),
                'store_name' => array(
                    'name' => 'store_name',
                    'vname' => 'LBL_STORE',
                    'required' => false,
                    'reportable' => true,
                    'audited' => true,
                    'importable' => 'true',
                    'massupdate' => false,
                    'source' => 'non-db',
                    'type' => 'relate',
                    'rname' => 'name',
                    'table' => 'accounts',
                    'id_name' => 'store_id',
                    'module' => 'Accounts',
                    'link' => 'store_link',
                ),
                'store_link' => array(
                    'name' => 'store_link',
                    'vname' => 'LBL_STORE',
                    'source' => 'non-db',
                    'type' => 'link',
                    'bean_name' => 'Account',
                    'relationship' => '_mymodule_store_accounts',
                    'module' => 'Accounts',
                ),
            ),
            'indices' => array(
                'idx__mymodules_store_id' => array(
                    'type' => 'index',
                    'name' => 'idx__mymodules_store_id',
                    'fields' => array('store_id'),
                ),
            ),
            'relationships' => array(
                '_mymodule_store_accounts' => array(
                    'relationship_type' => 'one-to-many',
                    'lhs_key' => 'id',
                    'lhs_module' => 'Accounts',
                    'lhs_table' => 'accounts',
                    'rhs_module' => '_MyModules',
                    'rhs_table' => '_mymodules',
                    'rhs_key' => 'store_id',
                ),
            ),
        );
        $m = $this->create();
        $m->addRelationships(array(
            'store' => 'Accounts',
        ));
        $d = $m->get();
        $this->assertEquals($real_dic, $d[$this->object_name]);
    }

    public function test_addContactRelationship()
    {
        $this->create()
            ->addRelationships(array(
                'Contacts',
            ))
            ->get();
    }

    public function test_addRelationshipRequired()
    {
        $m = $this->create();
        $m->addRelationships(array(
            'Accounts' => array('required' => true),
        ));
        $d = $m->get();
        $this->assertEquals(false, $d[$this->object_name]['fields']['account_id']['required']);
        $this->assertEquals(true, $d[$this->object_name]['fields']['account_name']['required']);
    }

    public function test_addRelationshipRequired2()
    {
        $m = $this->create();
        $m->addRelationships(array(
            'store' => array('module' => 'Accounts', 'required' => true),
        ));
        $d = $m->get();
        $this->assertEquals(false, $d[$this->object_name]['fields']['store_id']['required']);
        $this->assertEquals(true, $d[$this->object_name]['fields']['store_name']['required']);
    }

    public function test_addRelationshipNonRequired()
    {
        $m = $this->create();
        $m->addRelationships(array(
            'Accounts' => array('required' => false),
        ));
        $d = $m->get();
        $this->assertEquals(false, $d[$this->object_name]['fields']['account_id']['required']);
        $this->assertEquals(false, $d[$this->object_name]['fields']['account_name']['required']);
    }

    public function test_addRelationshipNonRequiredDefault()
    {
        $m = $this->create();
        $m->addRelationships(array(
            'Accounts',
        ));
        $d = $m->get();
        $this->assertEquals(false, $d[$this->object_name]['fields']['account_id']['required']);
        $this->assertEquals(false, $d[$this->object_name]['fields']['account_name']['required']);
    }

    public function test_addRelationshipVName()
    {
        $m = $this->create();
        $m->addRelationships(array(
            'Accounts' => array('vname' => 'LBL_FGHJ'),
        ));
        $d = $m->get();
        $this->assertEquals('LBL_FGHJ', $d[$this->object_name]['fields']['account_id']['vname']);
        $this->assertEquals('LBL_FGHJ', $d[$this->object_name]['fields']['account_name']['vname']);
        $this->assertEquals('LBL_FGHJ', $d[$this->object_name]['fields']['account_link']['vname']);
    }
}
