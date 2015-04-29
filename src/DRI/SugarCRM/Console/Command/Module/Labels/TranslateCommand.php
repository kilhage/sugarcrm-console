<?php

namespace DRI\SugarCRM\Console\Command\Module\Labels;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class TranslateCommand extends ApplicationCommand
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
        $this->setName('module:labels:translate')
            ->addArgument('module', InputArgument::REQUIRED, 'module to list labels in')
            ->addArgument('label', InputArgument::REQUIRED, 'module to list labels in')
            ->addOption('language', 'l', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_REQUIRED, '', array('default'))
            ->setDescription('Validates all labels in a module');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $languages = $this->languageManager->getLanguagesBasedOnOptions($input->getOption('language'));
        $module = $input->getArgument('module');
        $label = $input->getArgument('label');

        foreach ($languages as $language) {
            $this->languageManager->setCurrent($language);

            $output->writeln("<info>Translation for label $label in module $module on language $language are:</info>");
            $output->writeln($this->languageManager->translate($module, $label));
        }
    }
}
