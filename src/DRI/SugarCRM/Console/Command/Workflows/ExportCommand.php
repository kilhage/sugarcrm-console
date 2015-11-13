<?php

namespace DRI\SugarCRM\Console\Command\Workflows;

use DRI\SugarCRM\Console\Command\ApplicationCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ExportCommand extends ApplicationCommand
{
    /**
     * @var array
     */
    private static $links = array(
        'trigger_filters',
        'triggers',
        'alerts',
        'alerts',
        'actions',
    );

    /**
     * @var array
     */
    private static $fieldBlacklist = array (
        'id',
        'deleted',
        'date_entered',
        'date_modified',
        'modified_user_id',
        'created_by',
    );

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('workflows:export');
        $this->addArgument('id', InputArgument::OPTIONAL, 'if you only want to export a single workflow');
        $this->addOption('all', 'A', InputOption::VALUE_NONE, 'export all workflows available in the target directory');
        $this->addOption('directory', 'D', InputOption::VALUE_REQUIRED, 'target directive relative from the docroot', '../config/workflows');
        $this->setDescription('Export workflow records into .json files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!is_dir($input->getOption('directory'))) {
            mkdir($input->getOption('directory'), 0755, true);
        }

        if (null !== $this->input->getArgument('id')) {
            $this->export($this->input->getArgument('id'));
        } else {
            array_map(array ($this, 'export'), $this->listIds());
        }
    }

    /**
     * @return array
     * @throws \SugarQueryException
     */
    public function listIds()
    {
        $query = new \SugarQuery();
        $query->from(new \WorkFlow());
        $query->select('id');

        return array_map(function (array $row) {
            return $row['id'];
        }, $query->execute());
    }

    /**
     * @param string $id
     * @throws \Exception
     */
    public function export($id)
    {
        $workflow = \BeanFactory::retrieveBean('WorkFlow', $id);

        if (null === $workflow) {
            throw new \Exception('Unable to retrieve workflow with id: '.$id);
        }

        $data = $workflow->toArray();

        // remove fields in blacklist
        $data = array_diff_key($data, array_flip(self::$fieldBlacklist));

        foreach (self::$links as $link) {
            $workflow->load_relationship($link);
            $data[$link] = array_map(function (\SugarBean $bean) {
                $data = $bean->toArray();
                // remove fields in blacklist
                $data = array_diff_key($data, array_flip(self::$fieldBlacklist));
                return $data;
            }, $workflow->$link->getBeans());
        }

        $this->write($id, $data);
    }

    /**
     * @param string    $id
     * @param array     $data
     */
    private function write($id, array $data)
    {
        $file = sprintf('%s/%s.json', $this->input->getOption('directory'), $id);

        if (defined('JSON_PRETTY_PRINT')) {
            $content = json_encode($data, JSON_PRETTY_PRINT);
        } else {
            $content = json_encode($data);
        }

        if (file_exists($file)) {
            if (file_get_contents($file) !== $content) {
                file_put_contents($file, $content);
                $this->output->writeln("<comment>updating workflow file: $file</comment>");
            } else {
                $this->output->writeln("<info>workflow in file $file is already exported</info>");
            }
        } else {
            file_put_contents($file, $content);
            $this->output->writeln("<comment>creating workflow file: $file</comment>");
        }
    }
}
