<?php

namespace DRI\SugarCRM\Console\Command\Install;

use DRI\SugarCRM\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class InstallCommand extends Command
{
    /**
     * @var array
     */
    private static $required = array(
        'setup_db_database_name',
        'setup_license_key',
        'setup_site_url',
        'setup_site_admin_password',
    );

    private static $aliasMap = array(
        'setup_site_url' => 'u',
    );

    private static $parameterMap = array(

    );

    /**
     * @var array
     */
    private $config = array(
        'setup_db_host_name' => 'localhost',
        'setup_db_sugarsales_user' => 'root',
        'setup_db_sugarsales_password' => '',
        'setup_db_database_name' => null,
        'setup_db_type' => 'mysql',
        'demoData' => 'no',
        'setup_db_create_database' => true,
        'setup_db_create_sugarsales_user' => 1,
        'dbUSRData' => 'create',
        'setup_db_drop_tables' => true,
        'setup_db_username_is_privileged' => true,
        'setup_db_admin_user_name' => null,
        'setup_db_admin_password' => null,
        'setup_site_url' => null,
        'setup_site_admin_user_name' => 'admin',
        'setup_site_admin_password' => null,
        'setup_license_key' => '',
        'setup_site_sugarbeet_automatic_checks' => true,
        'default_currency_iso4217' => 'USD',
        'default_currency_name' => 'US Dollar',
        'default_currency_significant_digits' => '2',
        'default_currency_symbol' => '$',
        'default_date_format' => 'Y-m-d',
        'default_time_format' => 'H:i',
        'default_decimal_seperator' => '.',
        'default_export_charset' => 'ISO-8859-1',
        'default_language' => 'en_us',
        'default_locale_name_format' => 's f l',
        'default_number_grouping_seperator' => ',',
        'export_delimiter' => ',',
        'setup_system_name' => 'SugarCRM',
        'setup_fts_type' => 'Elastic',
        'setup_fts_host' => 'localhost',
        'setup_fts_port' => '9200',
    );

    /**
     *
     */
    protected function configure()
    {
        $this->setName('install')
            ->setDescription('Installs the sugarcrm app');

        foreach ($this->config as $key => $default) {
            $envDefault = $this->getKeyFromEnv($key);

            $name = isset(self::$parameterMap[$key]) ? self::$parameterMap[$key] : $key;
            $alias = isset(self::$aliasMap[$key]) ? self::$aliasMap[$key] : null;

            $this->addOption($name, $alias, InputOption::VALUE_REQUIRED, '', $envDefault !== false ? $envDefault : $default);
        }

        $this->addOption('auto_reinstall', 'r', InputOption::VALUE_NONE, $this->getKeyFromEnv('auto_reinstall') ?: null);
        $this->addOption('auto_drop_tables', 'a', InputOption::VALUE_NONE, $this->getKeyFromEnv('auto_drop_tables') ?: null);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkInstalled();

        $this->createConfigSi();

        try {
            $this->call($this->getOption('setup_site_url'));
        } catch (\Exception $e) {
            $this->removeConfigSi();
            throw $e;
        }

        $this->removeConfigSi();
    }

    /**
     *
     */
    private function checkDatabase()
    {
        $dbName = $this->getOption('setup_db_database_name');
        $dbType = $this->getOption('setup_db_type');

        if ($dbType !== 'mysql') {
            throw new \Exception("Unsupported db type: $dbType");
        }

        $db = new \mysqli(
            $this->getOption('setup_db_host_name'),
            $this->getOption('setup_db_admin_user_name'),
            $this->getOption('setup_db_admin_password')
        );

        if ($db->select_db($dbName)) {
            $auto_drop = $this->getOption('auto_drop_tables')
                || $this->askForKey(
                    'auto_drop_tables',
                    "<question>Database exist: $dbName, drop it?</question> <comment>(y/n)</comment>: ",
                    'n'
                ) === 'y';

            if ($auto_drop) {
                $this->output->writeln("<comment>Dropping table $dbName</comment>");
                $db->query("DROP DATABASE $dbName");
            } else {
                throw new \Exception("Database exist: $dbName");
            }
        } else {
            $this->output->writeln('<comment>DB does not exist</comment>');
        }
    }

    /**
     * @return bool
     */
    private function configExists()
    {
        $filePath = $this->getConfigPath();

        return file_exists($filePath);
    }

    /**
     * @return bool
     */
    private function removeConfig()
    {
        $filePath = $this->getConfigPath();
        $this->output->writeln("<comment>Removing $filePath</comment>");
        unlink($filePath);
    }

    /**
     * @throws \Exception
     */
    private function checkConfig()
    {
        if ($this->configExists()) {
            $autoReinstall = $this->getOption('auto_reinstall')
                || $this->askForKey(
                    'auto_reinstall',
                    '<question>SugarCRM already seem to be installed, reinstall?</question> <comment>(y/n)</comment>: ',
                    'n'
                ) === 'y';

            if ($autoReinstall) {
                $this->removeConfig();
            } else {
                throw new \Exception('SugarCRM already seem to be installed');
            }
        }
    }

    /**
     *
     */
    private function checkInstalled()
    {
        $this->checkDatabase();
        $this->checkConfig();
    }

    /**
     * @param $key
     * @param $question
     * @param $default
     *
     * @return mixed|string
     */
    private function getOption($key, $question = '<question>Please enter a value of config key %s:</question> ', $default = null)
    {
        $value = $this->input->getOption($key);

        if (is_null($value) && in_array($key, static::$required)) {
            $value = $this->getKeyFromEnv($key);
            $value = $value !== false ? $value : $this->askForKey($key, $question, $default);
        }

        $value = $this->fix($key, $value);

        $this->input->setOption($key, $value);

        return $value;
    }

    /**
     *
     */
    private function createConfigSi()
    {
        $config = array();

        foreach ($this->config as $key => $default) {
            $value = $this->getOption($key);

            $config[$key] = $value;
        }

        $filePath = $this->getConfigSiPath();

        $config = var_export($config, true);

        $data = <<<PHP
<?php

\$sugar_config_si = $config;

PHP;

        $this->output->writeln('<info>Creating config_si.php</info>');

        file_put_contents($filePath, $data);
    }

    /**
     * @param $key
     * @param $question
     * @param $default
     *
     * @return string
     */
    private function askForKey($key, $question, $default = '')
    {
        return $this->dialog->ask(
            $this->output,
            sprintf($question, $key),
            $default
        );
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getKeyFromEnv($key)
    {
        $prefix = 'SUGARCRM_DEFAULT_'.strtoupper($key);
        $value = getenv($prefix);

        return $value;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return string
     */
    private function fix($key, $value)
    {
        switch ($key) {
            case 'setup_site_url':
                if (strpos($value, 'http') !== 0) {
                    $value = "http://$value";
                    $this->output->writeln("<comment>Prepending url with http:// - result: $value</comment>");
                }
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getConfigSiPath()
    {
        $appPath = $this->getAppPath();
        $filePath = "$appPath/config_si.php";

        return $filePath;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        $appPath = $this->getAppPath();
        $filePath = "$appPath/config.php";

        return $filePath;
    }

    /**
     *
     */
    private function removeConfigSi()
    {
        $filePath = $this->getConfigSiPath();
        $this->output->writeln("<comment>Removing $filePath</comment>");
        unlink($filePath);
    }

    /**
     * @return mixed
     */
    private function getAppPath()
    {
        return $this->getApplication()->getSugar()->getAppPath();
    }

    /**
     * @param $url
     */
    private function call($url)
    {
        $si_results = '';

        $server_page = $url.'/install.php';

        $this->output->writeln("<info>Installing SugarCRM located at: $server_page ...</info>");

        $fh = fopen($server_page.'?goto=SilentInstall&cli=true', 'r') or die($php_errormsg);

        while (!feof($fh)) {
            $si_results .= fread($fh, 1048576);
        }

        $info = stream_get_meta_data($fh);

        fclose($fh);

        // message in a bottle
        preg_match('/<bottle>(.*)<\/bottle>/s', $si_results, $message);

        if (count($message) == 2) {
            // success
            $this->output->writeln("<info>{$message[1]}</info>");
        } else {
            // failure
            preg_match('/Exit (.*)/', $si_results, $message);

            if (count($message) == 2) {
                $this->output->writeln('<error>Error.  Most likely your configuration file is invalid.  Message returned was</error>');
            } else {
                if ($info['timed_out']) {
                    $this->output->writeln('<error>Error.  Connection timed out!</error>');
                } else {
                    $this->output->writeln("<error>Unknown error.  I don't know about this type of error message:</error>");
                }
            }
            $this->output->writeln($si_results);
        }
    }
}
