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
    private static $required = array (
        "setup_db_database_name",
        "setup_license_key",
        "setup_site_url",
        "setup_site_admin_password",
    );

    /**
     * @var array
     */
    private $config = array (
        'setup_db_host_name' => 'localhost',
        'setup_db_sugarsales_user' => 'root',
        'setup_db_sugarsales_password' => '',
        'setup_db_database_name' => null,
        'setup_db_type' => 'mysql',
        'setup_db_pop_demo_data' => false,
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
        $this->setName("install")
            ->setDescription("Installs the sugarcrm app");

        foreach ($this->config as $key => $default) {
            $envDefault = $this->getKeyFromEnv($key);

            $this->addOption($key, null, InputOption::VALUE_REQUIRED, "", $envDefault !== false ? $envDefault : $default);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createConfigSi();

        try {
            $this->call($this->getOption("setup_site_url"));
        } catch (\Exception $e) {
            $this->removeConfigSi();
            throw $e;
        }

        $this->removeConfigSi();
    }

    /**
     * @param $key
     *
     * @return mixed|string
     */
    private function getOption($key)
    {
        $value = $this->input->getOption($key);

        if (is_null($value) && in_array($key, static::$required)) {
            $value = $this->getKeyFromEnv($key);
            $value = $value !== false ? $value : $this->askForKey($key);
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
        $config = array ();

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

        $this->output->writeln("<info>Creating config_si.php</info>");

        file_put_contents($filePath, $data);
    }

    /**
     * @param $key
     *
     * @return string
     */
    private function askForKey($key)
    {
        return $this->dialog->ask(
            $this->output,
            "<question>Please enter a value of config key $key:</question> ",
            ''
        );
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getKeyFromEnv($key)
    {
        $prefix = "SUGARCRM_DEFAULT_" . strtoupper($key);
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
            case "setup_site_url":
                if (strpos($value, "http") !== 0) {
                    $this->output->writeln("<comment>Prepending url with http://</comment>");
                    return "http://$value";
                }

                break;
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
     *
     */
    private function removeConfigSi()
    {
        $filePath = $this->getConfigSiPath();
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
        $si_results = "";

        $server_page = $url . "/install.php";

        $this->output->writeln("<info>Installing SugarCRM located at: $server_page ...</info>");

        $fh = fopen($server_page . "?goto=SilentInstall&cli=true", "r") or die($php_errormsg);

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
                $this->output->writeln("<error>Error.  Most likely your configuration file is invalid.  Message returned was</error>");
            } else {
                if ($info['timed_out']) {
                    $this->output->writeln("<error>Error.  Connection timed out!</error>");
                } else {
                    $this->output->writeln("<error>Unknown error.  I don't know about this type of error message:</error>");
                }
            }
            print($si_results . "\n");
            exit(1);
        }
    }

}
