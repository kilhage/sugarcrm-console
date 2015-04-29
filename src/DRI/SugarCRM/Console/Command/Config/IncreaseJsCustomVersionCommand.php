<?php

namespace DRI\SugarCRM\Console\Command\Config;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class IncreaseJsCustomVersionCommand extends ApplicationCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('config:increase-js-custom-version')
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
        global $beanList;

        $sugar_version = \SugarConfig::getInstance()->get('sugar_version');

        $jsCustomVersion = \SugarConfig::getInstance()->get('js_custom_version', 1);
        $jsLangVersion = \SugarConfig::getInstance()->get('js_lang_version', 1);

        $jsCustomVersion++;
        $jsLangVersion++;

        $output->writeln("<info>Changing js_custom_version to: $jsCustomVersion</info>");
        $output->writeln("<info>Changing js_lang_version to: $jsLangVersion</info>");

        $sugar_config = loadCleanConfig();

        $sugar_config['js_custom_version'] = $jsCustomVersion;
        $sugar_config['js_lang_version'] = $jsLangVersion;

        rebuildConfigFile($sugar_config, $sugar_version);
    }
}
