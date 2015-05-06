<?php

namespace DRI\SugarCRM\Console\Command\Config;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class SetupForecastDefaultsCommand extends ApplicationCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('config:setup-forecast-defaults')
            ->addOption('truncate', 't', InputOption::VALUE_NONE)
            ->addOption('upgrade', 'u', InputOption::VALUE_NONE)
            ->setDescription('Increases the js custom version by 1');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once "modules/Forecasts/ForecastsDefaults.php";

        if ($input->getOption('truncate')) {
            \DBManagerFactory::getInstance()->query('DELETE FROM config WHERE category = "Forecasts"');
        }

        $settings = \ForecastsDefaults::setupForecastSettings($input->getOption('upgrade'));

        /** @var Table $table */
        $table = new Table($output);

        $table->setHeaders(array ('name', 'value'));

        foreach ($settings as $name => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $table->addRow(array ($name, $value));
        }

        $table->render();
    }
}
