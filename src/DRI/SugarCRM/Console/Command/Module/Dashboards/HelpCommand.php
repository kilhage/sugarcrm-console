<?php

namespace DRI\SugarCRM\Console\Command\Module\Dashboards;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class HelpCommand extends ApplicationCommand
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

    /**
     *
     */
    protected function configure()
    {
        $this->setName('module:dashboards:help')
            ->addArgument('module', InputArgument::REQUIRED, 'Target module')
            ->addOption('language', 'l', InputOption::VALUE_IS_ARRAY + InputOption::VALUE_OPTIONAL, 'A list of all languages to translate the labels into', array('default'))
            ->addOption('translation', 't', InputOption::VALUE_OPTIONAL, 'The translation for labels: LBL_HELP_CREATE, LBL_HELP_RECORD, LBL_HELP_RECORDS')
            ->addOption('create-translation', null, InputOption::VALUE_OPTIONAL, 'The translation for label: LBL_HELP_CREATE')
            ->addOption('record-translation', null, InputOption::VALUE_OPTIONAL, 'The translation for label: LBL_HELP_RECORD')
            ->addOption('list-translation', null, InputOption::VALUE_OPTIONAL, 'The translation for label: LBL_HELP_RECORDS')
            ->addOption('dry', null, InputOption::VALUE_NONE, 'Run the command without making any actual changes')
            ->setDescription('Sets up the dashboard definitions and labels inside a module');
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

        $languages = $this->languageManager->getLanguagesBasedOnOptions($input->getOption('language'));
        $translation = $input->getOption('translation');

        $this->ensureSideBarLayoutCreated($module);
        $this->ensureLocalTranslations($module, $languages);
    }

    /**
     * @param $module
     */
    private function ensureSideBarLayoutCreated($module)
    {
        $path = get_custom_file_if_exists("modules/$module/clients/base/layouts/sidebar/sidebar.php");

        if (!file_exists($path)) {
            $path = "custom/modules/$module/clients/base/layouts/sidebar/sidebar.php";

            $this->ensureDirectory($path);

            $content = <<<PHP
<?php

\$viewdefs['$module']['base']['layout']['sidebar'] = array (
  'components' => array (),
  'type' => 'simple',
  'span' => 12,
);

PHP;

            $this->write($path, $content);
        } else {
            $this->output->writeln("<info>sidebar definition already installed: $path</info>");
        }
    }

    /**
     * @param $module
     * @param array $languages
     */
    private function ensureLocalTranslations($module, array $languages)
    {
        $install = array();

        $translation = $this->input->getOption('translation');

        if (!empty($translation)) {
            $labels = array(
                'LBL_HELP_CREATE' => $translation,
                'LBL_HELP_RECORD' => $translation,
                'LBL_HELP_RECORDS' => $translation,
            );
        } else {
            $labels = array(
                'LBL_HELP_CREATE' => $this->input->getOption('create-translation'),
                'LBL_HELP_RECORD' => $this->input->getOption('record-translation'),
                'LBL_HELP_RECORDS' => $this->input->getOption('list-translation'),
            );
        }

        $labels = array_filter($labels);

        foreach ($languages as $language) {
            $mod_strings = $this->languageManager->getModuleLanguage($module, $language, true);

            foreach ($labels as $label => $trans) {
                if (!isset($mod_strings[$label])) {
                    $install[$language][$label] = $trans;
                } elseif ($mod_strings[$label] !== $trans) {
                    $install[$language][$label] = $trans;
                }
            }
        }

        if (empty($install)) {
            $this->output->writeln('<info>No translation to install</info>');
        } else {
            foreach ($install as $language => $labels) {
                $this->installTranslations($module, $language, $labels);
            }
        }
    }

    /**
     * @param $module
     * @param $language
     * @param $labels
     */
    private function installTranslations($module, $language, $labels)
    {
        $path = "custom/Extension/modules/$module/Ext/Language/$language.lang.php";

        if (!file_exists($path)) {
            $this->ensureDirectory($path);
        } else {
            include $path;
            $labels = array_merge($mod_strings, $labels);
        }

        $this->writeModStringsFile($path, $labels);
    }

    /**
     * @param $path
     * @param $labels
     */
    private function writeModStringsFile($path, $labels)
    {
        $content = "<?php\n\n";

        foreach ($labels as $label => $trans) {
            $content .= "\$mod_strings['$label'] = '$trans';\n";
        }

        $this->write($path, $content);
    }

    /**
     * @param $file
     */
    private function ensureDirectory($file)
    {
        $dir = dirname($file);

        if (!is_dir($dir)) {
            $this->output->writeln("<comment>Creating directory: $dir</comment>");

            if (!$this->input->getOption('dry')) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * @param $path
     * @param $content
     */
    private function write($path, $content)
    {
        $action = file_exists($path) ? 'Updating' : 'Creating';

        $this->output->writeln("<comment>$action file: $path</comment>");

        if (!$this->input->getOption('dry')) {
            file_put_contents($path, $content);
        }
    }
}
