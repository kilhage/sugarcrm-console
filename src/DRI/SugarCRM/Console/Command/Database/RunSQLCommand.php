<?php

namespace DRI\SugarCRM\Console\Command\Database;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @author Emil Kilhage
 */
class RunSQLCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName('db:sql')
            ->addArgument('sql', InputArgument::REQUIRED, 'The SQL')
            ->addOption('dry', 'd', InputOption::VALUE_NONE)
            ->setDescription('Executes an SQL query');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sql = $input->getArgument('sql');

        $output->write("<info>Running sql: '$sql' ... </info>");

        $db = \DBManagerFactory::getInstance();

        $result = $db->query($sql);

        if (is_object($result)) {
            $dumper = new VarDumper();

            $affectedRowCount = $db->getAffectedRowCount($result);

            $output->writeln("<comment>$affectedRowCount row(s) affected</comment>");

            while ($row = $db->fetchByAssoc($result)) {
                $dumper->dump($row);
            }
        } elseif (is_bool($result)) {
            if (!$result) {
                $output->writeLn("<error>Failed to run query, check sugarcrm.log for details</error>");
            } else {
                $output->writeln("<comment>Success!</comment>");
            }
        }
    }

}
