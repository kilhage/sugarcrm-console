<?php

namespace DRI\SugarCRM\Console\Command\Export\Module;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class FieldDefinitionsCommand extends ApplicationCommand
{
    /**
     * @var LanguageManager
     */
    private $languageManager;

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->languageManager = new LanguageManager();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('export:module:fields:definitions')
            ->addOption('module', 'm', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'white/black listed modules')
            ->addOption('translation', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, '')
            ->addOption('csv', null, InputOption::VALUE_NONE, 'Export definitions as csv')
            ->addOption('black-list-modules', 'b', InputOption::VALUE_NONE, 'Will black list all modules listed instead of white list')
            ->addOption('skip-non-db', 's', InputOption::VALUE_NONE, 'Skips all fields with source=non-db')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Target output file/stream', 'php://stdout')
            ->addOption('delimiter', 'd', InputOption::VALUE_REQUIRED, 'csv setting', ',')
            ->addOption('enclosure', 'e', InputOption::VALUE_REQUIRED, 'csv setting', '"')
            ->setDescription('Exports Module Definitions in different formats');
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
        if ($input->getOption('csv')) {
            $this->outputCsv();
        } else {
            $this->outputInline();
        }
    }

    /**
     *
     */
    private function outputInline()
    {
        /** @var TableHelper $table */
        $table = $this->getHelper('table');
        $table->setHeaders($this->getHeaders());
        $table->setRows($this->getRows());
        $table->render($this->output);
    }

    /**
     *
     */
    private function outputCsv()
    {
        $fileName = $this->input->getOption('output');

        $handle = fopen($fileName, 'w+');

        $this->fputcsv($handle, $this->getHeaders());

        foreach ($this->getRows() as $row) {
            $this->fputcsv($handle, $row);
        }
    }

    /**
     * @param $handle
     * @param array $columns
     */
    private function fputcsv($handle, array $columns)
    {
        fputcsv(
            $handle,
            $columns,
            $this->input->getOption('delimiter'),
            $this->input->getOption('enclosure')
        );
    }

    /**
     * @return array
     */
    private function getRows()
    {
        $modules = $this->getModules();
        $translations = $this->input->getOption('translation');

        $rows = array();

        foreach ($modules as $module) {
            $bean = \BeanFactory::getBean($module);

            if (!$bean instanceof \SugarBean) {
                continue;
            }

            $defs = $bean->getFieldDefinitions();

            if (empty($defs)) {
                continue;
            }

            foreach ($defs as $def) {
                if ($this->input->getOption('skip-non-db')) {
                    if (!empty($def['source']) && $def['source'] == 'non-db') {
                        continue;
                    }
                }

                $row = array(
                    $module,
                    $def['name'],
                    !empty($def['vname']) ? $def['vname'] : '',
                );

                foreach ($translations as $translation) {
                    $this->languageManager->setCurrent($translation);
                    $row[] = !empty($def['vname']) ? $this->languageManager->translate($module, $def['vname']) : '';
                }

                $row = array_merge($row, array(
                    !empty($def['type'])     ? $def['type'] : '',
                    !empty($def['dbType'])   ? $def['dbType'] : '',
                    !empty($def['len'])      ? $def['len'] : '',
                    !empty($def['source'])   ? $def['source'] : '',
                    !empty($def['required']) ? 'true' : 'false',
                    !empty($def['default'])  ? $def['default'] : '',
                    !empty($def['comment'])  ? $def['comment'] : '',
                ));

                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        $translations = $this->input->getOption('translation');

        $headers = array(
            '_module',
            'name',
            'vname',
        );

        foreach ($translations as $translation) {
            $headers[] = "translation ($translation)";
        }

        $headers = array_merge($headers, array(
            'type',
            'dbType',
            'len',
            'source',
            'required',
            'default',
            'comment',
        ));

        return $headers;
    }

    /**
     * @return array|mixed
     */
    private function getModules()
    {
        global $beanList;

        $modules = $this->input->getOption('module');

        if (empty($modules) || $this->input->getOption('black-list-modules')) {
            $allModules = array_keys($beanList);

            if ($this->input->getOption('black-list-modules')) {
                foreach ($modules as $module) {
                    $key = array_search($module, $allModules);

                    if ($key) {
                        unset($allModules[$key]);
                    }
                }
            }

            $modules = $allModules;
        }

        return $modules;
    }
}
