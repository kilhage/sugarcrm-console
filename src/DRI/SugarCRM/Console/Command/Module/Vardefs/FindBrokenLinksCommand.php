<?php

namespace DRI\SugarCRM\Console\Command\Module\Vardefs;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class FindBrokenLinksCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('module:vardefs:find-broken-links')
            ->setDescription('Finds broken links in all modules');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $beanList;

        $broken = 0;
        $total = 0;

        foreach ($beanList as $module_name => $object_name) {
            $bean = \BeanFactory::getBean($module_name);
            if ($bean instanceof \SugarBean && is_array($bean->field_defs)) {
                foreach ($bean->field_defs as $field_name => $def) {
                    if ($def["type"] == "link" && (!$bean->load_relationship($field_name) || !is_object($bean->{$field_name}))) {
                        $output->writeln("<error>{$module_name}->{$field_name} is broken</error>");

                        $broken++;
                    }
                    $total++;
                }
            }
        }

        echo "Broken: $broken\n";
        echo "Total: $total\n";
    }
}
