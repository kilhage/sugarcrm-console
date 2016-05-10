<?php

namespace DRI\SugarCRM\Console\Command\Generate;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Oskar Hellgren
 */
class M2mCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName('generate:m2m')
            ->addArgument('module1', InputArgument::REQUIRED, 'Module1')
            ->addArgument('module2', InputArgument::REQUIRED, 'Module2')
            ->addOption('table', 't', InputOption::VALUE_OPTIONAL)
            ->addOption('relationship', 'r', InputOption::VALUE_OPTIONAL)
            ->addOption('force', 'f', InputOption::VALUE_NONE)
            ->setDescription('Generate files for M2M-relationship');
    }

    /**
     * @global array $beanList
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $beanList;
        $module1   = $input->getArgument('module1');
        $module2   = $input->getArgument('module2');
        $force     = $input->getOption('force');
        $tableName = strtolower($input->getOption('table'));
        $relName   = strtolower($input->getOption('relationship'));

        if (!isset($beanList[$module1])) {
            throw new \Exception("Cannot find module '$module1'");
        }
        if (!isset($beanList[$module2])) {
            throw new \Exception("Cannot find module '$module2'");
        }

        if (empty($relName)) {
            $relName = strtolower($module1."_".$module2);
        }

        \SugarRelationshipFactory::getInstance(); // Loads avalible rels into global variables
        if (isset($GLOBALS['relationships'][$relName])) {
            throw new \Exception("Relationship $relName already exists");
        }

        if (empty($tableName)) {
            $tableName = strtolower($module1."_".$module2);
        }
        $db = \DBManagerFactory::getInstance();
        if ($db->tableExists($tableName) && $force != true) {
            throw new \Exception("Table $tableName already exists in database. Add option --force if you want to use this table (USE CAUTION!) or use option --table to specify another table name");
        }

        if ($this->writeMetaData($module1, $module2, $relName, $tableName)) {
            $this->writeTableDictionary($relName);
            $this->writeLinks($module1, $module2, $relName);
        } else {
            $output->writeLn("<error>Could not write medatada file</error>");
        }
    }

    /**
     * Write vardefs for links
     *
     * @param string $module1
     * @param string $module2
     * @param string $relName
     */
    protected function writeLinks($module1, $module2, $relName)
    {
        $object1 = \BeanFactory::getObjectName($module1);
        $object2 = \BeanFactory::getObjectName($module2);

        // Add link from $module1 to $module2
        $path = "custom/Extension/modules/$module1/Ext/Vardefs";
        if (!is_dir($path)) {
            mkdir_recursive($path, true);
        }
        $contents = '<?php

$dictionary["'.$object1.'"]["fields"]["'.strtolower($module2).'"] = array (
    "name" => "'.strtolower($module2).'",
    "vname" => "LBL_'.strtoupper($module2).'",
    "source" => "non-db",
    "type" => "link",
    "bean_name" => "'.$object2.'",
    "relationship" => "'.$relName.'",
    "module" => "'.$module2.'",
);

';
        echo "* Writing $path/$relName"."_link.php \n";
        if (!file_put_contents($path."/".$relName."_link.php", $contents)) {
            echo "Error: Could not write to file \n";
        }

        // Add link from $module2 to $module1
        $path = "custom/Extension/modules/$module2/Ext/Vardefs";
        if (!is_dir($path)) {
            mkdir_recursive($path, true);
        }
        $contents = '<?php

$dictionary["'.$object2.'"]["fields"]["'.strtolower($module1).'"] = array (
    "name" => "'.strtolower($module1).'",
    "vname" => "LBL_'.strtoupper($module1).'",
    "source" => "non-db",
    "type" => "link",
    "bean_name" => "'.$object1.'",
    "relationship" => "'.$relName.'",
    "module" => "'.$module1.'",
);

';
        echo "* Writing $path/$relName"."_link.php \n";
        if (!file_put_contents($path.'/'.$relName.'_link.php', $contents)) {
            echo "Error: Could not write to file \n";
        }
    }

    /**
     * Write the relationship & table-def to file
     *
     * @param string $module1
     * @param string $module2
     * @param string $relName
     * @param string $tableName
     * @return int|bool
     */
    protected function writeMetaData($module1, $module2, $relName, $tableName)
    {
        /* @var \SugarBean $bean1 */
        $bean1 = \BeanFactory::getBean($module1);
        /* @var \SugarBean $bean2 */
        $bean2 = \BeanFactory::getBean($module2);

        $contents = '<?php

$dictionary["'.$relName.'"] = array (
  "true_relationship_type" => "many-to-many",
  "relationships" =>  array (
    "'.$relName.'" => array (
      "lhs_module" => "'.$module1.'",
      "lhs_table" => "'.$bean1->table_name.'",
      "lhs_key" => "id",
      "rhs_module" => "'.$module2.'",
      "rhs_table" => "'.$bean2->table_name.'",
      "rhs_key" => "id",
      "relationship_type" => "many-to-many",
      "join_table" => "'.$tableName.'",
      "join_key_lhs" => "'.strtolower($bean1->object_name).'_id",
      "join_key_rhs" => "'.strtolower($bean2->object_name).'_id",
    ),
  ),
  "table" => "'.$tableName.'",
  "fields" =>  array (
    array (
      "name" => "id",
      "type" => "varchar",
      "len" => 36,
    ),
    array (
      "name" => "'.strtolower($bean1->object_name).'_id",
      "type" => "varchar",
      "len" => 36,
    ),
    array (
      "name" => "'.strtolower($bean2->object_name).'_id",
      "type" => "varchar",
      "len" => 36,
    ),
    array (
      "name" => "date_modified",
      "type" => "datetime",
    ),
    array (
      "name" => "deleted",
      "type" => "bool",
      "len" => "1",
      "default" => "0",
      "required" => true,
    ),
  ),
  "indices" =>  array (
    array (
      "name" => "'.$relName.'_pk",
      "type" => "primary",
      "fields" => array ("id"),
    ),
    array (
      "name" => "'.strtolower($bean1->object_name).'_idx",
      "type" => "index",
      "fields" => array ("'.strtolower($bean1->object_name).'_id"),
    ),
    array (
      "name" => "'.strtolower($bean2->object_name).'_idx",
      "type" => "index",
      "fields" => array ("'.strtolower($bean2->object_name).'_id"),
    ),
  ),
);

';

        $path = "custom/metadata";
        if (!is_dir($path)) {
            mkdir_recursive($path, true);
        }

        echo "* Writing $path/$relName"."MetaData.php \n";
        return file_put_contents($path.'/'.$relName.'MetaData.php', $contents);
    }

    /**
     * @param string $relName
     */
    protected function writeTableDictionary($relName)
    {
        $path = "custom/Extension/application/Ext/TableDictionary";
        if (!is_dir($path)) {
            mkdir_recursive($path, true);
        }

        $contents = "<?php
include('custom/metadata/".$relName."MetaData.php');
";
        echo "* Writing $path/$relName.php \n";
        if (!file_put_contents($path.'/'.$relName.'.php', $contents)) {
            echo "Error: Could not write to file \n";
        }
    }
}