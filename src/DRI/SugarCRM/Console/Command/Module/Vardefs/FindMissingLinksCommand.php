<?php

namespace DRI\SugarCRM\Console\Command\Module\Vardefs;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class FindMissingLinksCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('module:vardefs:find-missing-links')
            ->addArgument('filter')
            ->setDescription('Finds missing links in all modules');
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

        $missing = 0;
        $total = 0;

        foreach ($beanList as $module_name => $object_name) {

            if ($input->getArgument('filter') && !preg_match($input->getArgument('filter'), $module_name)) {
                continue;
            }

            $bean = \BeanFactory::getBean($module_name);
            if ($bean instanceof \SugarBean && is_array($bean->field_defs)) {
                foreach ($bean->field_defs as $field_name => $def) {
                    if ($def['type'] === 'link' && $bean->load_relationship($field_name) && is_object($bean->{$field_name})) {

                        /** @var \SugarRelationship $rel */
                        $rel = $bean->{$field_name}->getRelationshipObject();

                        if ($rel) {
                            $lhsLink = $rel->getLHSLink();
                            $rhsLink = $rel->getRHSLink();

                            if (empty($lhsLink) || empty($rhsLink)) {
                                $output->writeln("<error>{$module_name}->{$field_name} is broken, missing link with relationship {$def['relationship']}</error>");
                                $missing++;
                            }
                        } else {
                            $output->writeln("<error>{$module_name}->{$field_name} is broken, unable to load relationship {$def['relationship']}</error>");
                        }
                    }

                    $total++;
                }
            }
        }

        echo "Missing: $missing\n";
        echo "Total: $total\n";
    }
}
