<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class CustomFieldsCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('repair:custom-fields')
            ->addOption('dry', null, InputOption::VALUE_NONE, "Only print the sql's")
            ->setDescription('Repair All Custom Fields');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Repairing all custom tables</info>');

        $dry = $input->getOption('dry');

        $execute = !$dry;

        global $beanList;

        require_once 'modules/DynamicFields/FieldCases.php';

        foreach ($beanList as $module_name => $object_name) {
            $bean = \BeanFactory::getBean($module_name);

            if ($bean instanceof \SugarBean) {
                $defs = $bean->getFieldDefinitions();

                if (is_array($defs)) {
                    foreach ($defs as $field_name => $def) {
                        if (!empty($def['custom_module'])) {
                            $df = new \DynamicField($bean->module_dir);

                            $df->bean = $bean;
                            $sql = $df->repairCustomFields($execute);

                            if (!empty($sql)) {
                                echo $sql;
                            }
                        }
                    }
                }
            }
        }

        $output->writeln('<info>Done</info>');
    }
}
