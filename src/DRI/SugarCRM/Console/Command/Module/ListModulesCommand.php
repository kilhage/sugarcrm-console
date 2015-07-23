<?php

namespace DRI\SugarCRM\Console\Command\Module;

use DRI\SugarCRM\Console\Application as Sugar;
use DRI\SugarCRM\Console\Command;
use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @author Emil Kilhage
 */
class ListModulesCommand extends ApplicationCommand
{
    /**
     * @var LanguageManager
     */
    private $languageManager;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->languageManager = new LanguageManager();
    }

    protected function configure()
    {
        $this->setName('module:list')
            ->addOption('custom', null, InputOption::VALUE_NONE)
            ->addOption('ootb', null, InputOption::VALUE_NONE)
            ->addOption('all', null, InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modules = array ();
        $custom = $this->getCustomModules();
        if ($input->getOption('custom')) {
            $modules = $custom;
        } elseif ($input->getOption('ootb')) {
            foreach ($GLOBALS['beanList'] as $moduleName => $beanName) {
                if (!in_array($beanName, $custom)) {
                    $modules[$moduleName] = $beanName;
                }
            }
        } elseif ($input->getOption('all')) {
            $modules = $GLOBALS['beanList'];
        } else {
            $modules = $GLOBALS['beanList'];
        }

        $dumper = new VarDumper();
        $dumper->dump($modules);
    }

    /**
     * @return array
     */
    private function getCustomModules()
    {
        $files = glob("custom/Extension/application/Ext/Include/*.php");
        $beanList = array ();

        foreach ($files as $file) {
            include $file;
        }

        return $beanList;
    }
}
