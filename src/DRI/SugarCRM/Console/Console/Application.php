<?php

namespace DRI\SugarCRM\Console\Console;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Console\Command\SugarAwareCommand;
use DRI\SugarCRM\Console\Application as Sugar;
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
     * @var array
     */
    private $commands = array ();

    /**
     * @param Sugar $sugar
     */
    public function __construct(Sugar $sugar)
    {
        $this->sugar = $sugar;

        parent::__construct('SugarCRM-Console', '0.0.1');

        $this->getDefinition()->addOption(new InputOption('--sugar_path', null, InputOption::VALUE_OPTIONAL, 'Path to SugarCRM Application'));
        $this->getDefinition()->addOption(new InputOption('--current_user', null, InputOption::VALUE_OPTIONAL, 'The current user id to run the script under'));

        $this->registerCommands();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        return parent::doRun($input, $output);
    }

    /**
     *
     */
    private function registerCommands()
    {
        $this->_addCommands(
            dirname(dirname(dirname(dirname(__DIR__)))) . "/",
            dirname(__DIR__) . "/Command/"
        );
    }

    /**
     * @param array|\Symfony\Component\Console\Command\Command[] $prefix
     * @param $dir
     * @throws \LogicException
     */
    private function _addCommands($prefix, $dir)
    {
        $commandFiles = $this->getFiles($prefix, $dir);

        foreach ($commandFiles as $class_name) {
            $refl = new \ReflectionClass($class_name);

            if (!$refl->isAbstract()) {
                $command = new $class_name();

                if (!($command instanceof \DRI\SugarCRM\Console\Command)) {
                    throw new \LogicException();
                }

                if ($command instanceof SugarAwareCommand) {
                    $command->setSugar($this->sugar);
                }

                $this->add($command);

                $this->commands[$command->getName()] = $command;
            }
        }
    }

    /**
     * @return array
     */
    private function getCommands()
    {
        return $this->commands;
    }

    /**
     * @param string $name
     *
     * @return \DRI\SugarCRM\Console\Command
     * @throws \LogicException
     */
    private function getCommand($name)
    {
        if (!isset($this->commands[$name])) {
            throw new \LogicException("Invalid command name: {$name}");
        }

        return $this->commands[$name];
    }

    /**
     * @param $command_name
     *
     * @return bool
     */
    public function isApplicationCommand($command_name)
    {
        $command = $this->getCommand($command_name);
        return $command instanceof ApplicationCommand;
    }

    /**
     * @param $prefix
     * @param $path
     *
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
            $class_name = str_replace($prefix, "", $file_path);
            $class_name = str_replace("/", "\\", rtrim($class_name, ".php"));

            $commands[] = $class_name;
        }

        return $commands;
    }

}
