<?php

namespace Kilhage\SugarCRM\Console;

use Kilhage\SugarCRM\Command\SugarAwareCommand;
use Kilhage\SugarCRM\Application as Sugar;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class Application extends BaseApplication
{

    /**
     * @var Sugar
     */
    private $sugar;

    /**
     * @param Sugar $sugar
     */
    public function __construct(Sugar $sugar)
    {
        $this->sugar = $sugar;

        parent::__construct('SugarCRM-Console', '0.0.1');

        $this->getDefinition()->addOption(new InputOption('--sugar_path', null, InputOption::VALUE_OPTIONAL, 'Path to SugarCRM Application'));
        $this->getDefinition()->addOption(new InputOption('--current_user', null, InputOption::VALUE_OPTIONAL, 'The current user id to run the script under'));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->registerCommands();

        return parent::doRun($input, $output);
    }

    /**
     *
     */
    private function registerCommands()
    {
        $dir = dirname(__DIR__) . "/Command/";
        $commands = $this->getFiles($dir);

        foreach ($commands as $class_name => $file_path) {
            $refl = new \ReflectionClass($class_name);

            if (!$refl->isAbstract()) {
                $command = new $class_name();

                if ($command instanceof SugarAwareCommand) {
                    $command->setSugar($this->sugar);
                }

                $this->add($command);
            }
        }
    }

    /**
     * @param $path
     * @return array
     */
    private function getFiles($path)
    {
        $path = rtrim($path, "/");
        $files = scandir($path);
        $files = array_diff($files, array('.', '..'));

        $return = array ();

        foreach ($files as $file) {
            if (is_file("$path/$file")) {

                $file_path = "$path/$file";

                $class_name = ltrim($file_path, dirname(dirname(dirname(__DIR__))) . "/");
                $class_name = str_replace("/", "\\", rtrim($class_name, ".php"));

                $return[$class_name] = $file_path;
            } elseif (is_dir("$path/$file")) {
                $return = array_merge($return, $this->getFiles("$path/$file"));
            }
        }

        return $return;
    }

}
