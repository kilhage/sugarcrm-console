<?php

namespace DRI\SugarCRM\Console\Command\Export\Module;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use InvalidArgumentException;

/**
 * @author Emil Kilhage
 */
class RelationshipDefinitionsCommand extends ApplicationCommand
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
        $this->setName("export:module:relationships:definitions")
            ->addOption("module", "m", InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "white/black listed modules")
            ->addOption("csv", null, InputOption::VALUE_NONE, "Export definitions as csv")
            ->addOption("black-list-modules", "b", InputOption::VALUE_NONE, "Will black list all modules listed instead of white list")
            ->addOption("output", "o", InputOption::VALUE_REQUIRED, "Target output file/stream", "php://stdout")
            ->addOption("delimiter", "d", InputOption::VALUE_REQUIRED, "csv setting", ",")
            ->addOption("enclosure", "e", InputOption::VALUE_REQUIRED, "csv setting", '"')
            ->setDescription("Exports Module Definitions in different formats");
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
        if ($input->getOption("csv")) {
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
        $fileName = $this->input->getOption("output");

        $handle = fopen($fileName, "w+");

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
            $this->input->getOption("delimiter"),
            $this->input->getOption("enclosure")
        );
    }

    /**
     * @return array
     */
    private function getRows()
    {
        $modules = $this->getModules();

        $rows = array ();

        $relationships = \SugarRelationshipFactory::getInstance()->getRelationshipDefs();

        foreach ($relationships as $name => $relationship) {
            $row = array ();

            $add = false;

            foreach ($modules as $module) {
                if (!empty($relationship["lhs_module"]) && $relationship["lhs_module"] == $module) {
                    $add = true;
                }

                if (!empty($relationship["rhs_module"]) && $relationship["rhs_module"] == $module) {
                    $add = true;
                }
            }

            if (!$add) {
                continue;
            }

            $row[] = $name;

            foreach ($this->getHeaders() as $column) {
                if (!empty($relationship[$column])) {
                    $row[] = $relationship[$column];
                }
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return array
     */
    private function getHeaders()
    {
        $headers = array (
            'relationship_name',
            'lhs_module',
            'lhs_table',
            'lhs_key',
            'rhs_module',
            'rhs_table',
            'rhs_key',
            'join_table',
            'join_key_lhs',
            'join_key_rhs',
            'relationship_type',
            'relationship_role_column',
            'relationship_role_column_value',
        );

        return $headers;
    }

    /**
     * @return array|mixed
     */
    private function getModules()
    {
        global $beanList;

        $modules = $this->input->getOption("module");

        if (empty($modules) || $this->input->getOption("black-list-modules")) {
            $allModules = array_keys($beanList);

            if ($this->input->getOption("black-list-modules")) {
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
