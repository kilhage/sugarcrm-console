<?php

namespace DRI\SugarCRM\Console\Command\Export\Studio\Fields;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ToFilesCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("export:studio:fields:to-files")
            ->addOption("dry", null, InputOption::VALUE_NONE, "Only output the sql's that will be executed")
            ->addOption("remove-field-from-db", null, InputOption::VALUE_NONE, "Removes the field from the fields_meta_data if successful")
            ->addOption("output-content", null, InputOption::VALUE_NONE, "Outputs the content that will be written")
            ->setDescription("Exports Studio Fields To Files");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Exporting Studio Fields");

        $dry = $input->getOption("dry");
        $remove_field_from_db = $input->getOption("remove-field-from-db");
        $output_content = $input->getOption("output-content");

        $app_path = $this->getSugar()->getAppPath();

        $db = \DBManagerFactory::getInstance();
        $sql = "SELECT * FROM fields_meta_data";
        $result = $db->query($sql);

        while ($row = $db->fetchByAssoc($result))
        {
            $module = $row['custom_module'];
            $field_name = $row['name'];
            $id = $row['id'];

            $bean = \BeanFactory::getBean($module);

            if ($bean instanceof \SugarBean) {
                $def = $bean->getFieldDefinition($field_name);
                $def_str = var_export($def, true);
                $object_name = $bean->getObjectName();

                $content = <<<PHP
<?php

\$dictionary['$object_name']['custom_fields'] = true:
\$dictionary['$object_name']['fields']['$field_name'] = $def_str;

PHP;

                $dir = "$app_path/custom/Extension/modules/$module/Ext/Vardefs";
                $path = "$dir/sugarfield_{$field_name}.php";

                if (!is_dir($dir)) {
                    $output->writeln("Creating directory: $dir");
                    if (!mkdir($dir, 755, true)) {
                        throw new \Exception("Unable to create directory: $dir");
                    }
                }

                $output->writeln("Exporting field $module:$field_name to file: $path");

                if ($output_content) {
                    $output->writeln("$content");
                }

                if (!$dry) {
                    if (file_put_contents($path, $content) === false) {
                        throw new \Exception("Unable to write to file: ");
                    }
                }

                if ($remove_field_from_db) {
                    $output->writeln("Removing field from fields_meta_data");
                    if (!$dry) {
                        $db->query("DELETE FROM fields_meta_data WHERE id = '{$id}'");
                    }
                }
            }
        }

        $output->writeln("Done");
    }

}
