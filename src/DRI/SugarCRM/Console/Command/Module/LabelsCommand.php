<?php

namespace DRI\SugarCRM\Console\Command\Module;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class LabelsCommand extends ApplicationCommand
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
        $this->setName("module:labels")
            ->addArgument("module", InputArgument::REQUIRED, "module to list labels in")
            ->addOption("language", 'l', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_REQUIRED, "", array ('default'))
            ->setDescription("Lists all labels in a module");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $beanList;
        $preg = $input->getArgument("module");
        $languages = $this->languageManager->getLanguagesBasedOnOptions($input->getOption("language"));

        if (strpos($preg, "/") !== 0) {
            $preg = "/^$preg$/";
        }

        $modules = array_keys($beanList);
        foreach ($languages as $language) {
            $this->languageManager->setCurrent($language);
            foreach ($modules as $module)
            {
                if (!preg_match($preg, $module))
                    continue;

                $labels = $this->languageManager->getModuleLanguage($module, $language, true);

                $output->writeln(" - $module:");
                $output->writeln(var_export($labels, true));
                $output->writeln("===================================");
            }
        }
    }

}
