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
use Symfony\Component\Finder\Finder;

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
        $this->_addCommands(
            dirname(dirname(dirname(__DIR__))) . "/",
            dirname(__DIR__) . "/Command/"
        );
    }

    /**
     * @param array|\Symfony\Component\Console\Command\Command[] $prefix
     * @param $dir
     */
    private function _addCommands($prefix, $dir)
    {
        $commands = $this->getFiles($prefix, $dir);

        foreach ($commands as $class_name) {
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
    private function getFiles($prefix, $path)
    {
        $commands = array ();
        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->name('*.php')
            ->in($path);

        foreach ($iterator as $file) {
            $file_path = $file->getRealpath();
            $class_name = ltrim($file_path, $prefix);
            $class_name = str_replace("/", "\\", rtrim($class_name, ".php"));

            $commands[] = $class_name;
        }

        return $commands;
    }

}
