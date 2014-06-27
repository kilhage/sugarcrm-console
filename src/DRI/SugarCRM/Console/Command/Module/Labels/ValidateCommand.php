<?php

namespace DRI\SugarCRM\Console\Command\Module\Labels;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class ValidateCommand extends ApplicationCommand
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
        $this->setName("module:labels:validate")
            ->addArgument("module", InputArgument::REQUIRED, "module to validate labels in")
            ->addOption("language", 'l', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_REQUIRED, "", array ('default'))
            ->addOption("local", 'lo', InputOption::VALUE_REQUIRED, "", "false")
            ->addOption("format", 'fo', InputOption::VALUE_REQUIRED, "", "array")
            ->addOption("variableName", 'vn', InputOption::VALUE_OPTIONAL, "", "mod_strings")
            ->addOption("base", null, InputOption::VALUE_IS_ARRAY + InputOption::VALUE_REQUIRED, "", array ("default"))
            ->setDescription("Validates all labels in a module");
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
        $languages = $input->getOption("language");
        $variableName = $input->getOption("variableName");
        $format = $input->getOption("format");
        $baseLanguages = $input->getOption("base");
        $local = $input->getOption("local") === "true";

        if (strpos($preg, "/") !== 0) {
            $preg = "/^$preg$/";
        }

        $languages = $this->languageManager->getLanguagesBasedOnOptions($languages);
        $baseLanguages = $this->languageManager->getLanguagesBasedOnOptions($baseLanguages);

        $modules = array_keys($beanList);
        foreach ($languages as $language) {
            $this->languageManager->setCurrent($language);
            $this->languageManager->setDefault($language);
            foreach ($modules as $module)
            {
                if (!preg_match($preg, $module))
                    continue;

                $missingLabels = $this->languageManager->getMissingLabelsInModule($module, $language, $local, $baseLanguages);

                if (!empty($missingLabels)) {
                    $output->writeln("<error>Missing labels for module $module in language $language:</error>");

                    switch ($format) {
                        case "array":
                            $output->write(var_export($missingLabels, true));
                            break;
                        case "variables":
                        case "vars":
                            foreach ($missingLabels as $label => $trans) {
                                $output->writeln("\${$variableName}['$label'] = '$trans';");
                            }
                            break;
                    }
                } else {
                    $output->writeln("<info>$module is ok in language $language:</info>");
                }
            }
        }
    }

}
