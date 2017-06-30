<?php

namespace DRI\SugarCRM\Console\Command\Export;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class DataCommand extends ApplicationCommand
{
    const LIMIT = 500;

    protected function configure()
    {
        $this->setName('export:data')
            ->addArgument('module', InputArgument::REQUIRED)
            ->addArgument('file', InputArgument::REQUIRED)
            ->addOption('column', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED)
            ->setDescription('Exports data from a module to a .csv');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tpl = \BeanFactory::newBean($input->getArgument('module'));
        $offset = 0;

        $query = new \SugarQuery();
        $query->from($tpl);
        $query->limit(self::LIMIT);
        $query->offset($offset);

        $handle = fopen($input->getArgument('file'), 'wb+');
        $output->writeln('Starting export of '.$tpl->module_dir);
        $output->writeln('Writing data to '.$input->getArgument('file'));

        $fields = count($input->getOption('column')) > 0 ? $input->getOption('column') : $this->getFieldNames($tpl);

        fputcsv($handle, $fields);

        do {
            $output->writeln('fetching results '.$offset.' to '.($offset + self::LIMIT - 1));
            /** @var \SugarBean[] $beans */
            $beans = $tpl->fetchFromQuery($query);

            foreach ($beans as $bean) {
                $row = array ();

                foreach ($fields as $field) {
                    $def = $bean->getFieldDefinition($field);

                    switch ($def['type']) {
                        case 'bool':
                            $row[] = $bean->{$field} ? 1 : 0;
                            break;
                        default:
                            $row[] = $bean->{$field};
                            break;
                    }
                }

                fputcsv($handle, $row);
            }

            $offset += self::LIMIT;
            $query->offset($offset);
        } while (count($beans) === self::LIMIT);
    }

    /**
     * @param \SugarBean $bean
     * @return array
     */
    private function getFieldNames(\SugarBean $bean)
    {
        $fields = array ();

        foreach ($bean->getFieldDefinitions() as $def) {
            if (!in_array($def['type'], array('link'), true)) {
                $fields[] = $def['name'];
            }
        }

        return $fields;
    }
}
