<?php

namespace DRI\SugarCRM\Console\Command\Module\Labels;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Emil Kilhage
 */
class MigrateToExtensionCommand extends ApplicationCommand
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
        $this->setName('module:labels:migrate-to-extensions')
            ->addArgument('module', InputArgument::OPTIONAL, 'module to list labels in')
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
        $module = $input->getArgument('module');

        if (empty($module)) {
            foreach (array_keys($GLOBALS['beanList']) as $module) {
                $this->migrateModule($module);
            }
        } else {
            $this->migrateModule($module);
        }
    }

    /**
     * @param $module
     */
    private function migrateModule($module)
    {
        $directory = "custom/modules/$module/language";

        if (!is_dir($directory)) {
            return;
        }

        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->name('*.lang.php')
            ->in($directory);

        foreach ($iterator as $file) {
            $this->migrateModuleFile($module, $file);
        }
    }

    /**
     * @param $module
     * @param SplFileInfo $file
     */
    private function migrateModuleFile($module, SplFileInfo $file)
    {
        $filePath = $file->getRealpath();
        $fileName = $file->getFileName();

        list($locale) = explode('.', $fileName);

        require $filePath;

        $this->languageManager->addLabelsToDefaultExtFile($module, $locale, $mod_strings);

        $this->output->writeln("Unlinking $filePath");
        unlink($filePath);
    }
}
