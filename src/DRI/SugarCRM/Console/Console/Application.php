<?php

namespace DRI\SugarCRM\Console\Console;

use DRI\SugarCRM\Console\Command\SugarAwareCommand;
use DRI\SugarCRM\Console\Application as Sugar;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;
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

        parent::__construct('SugarCRM-Console', '0.21.0');

        $this->getDefinition()->addOption(new InputOption('--sugar_path', null, InputOption::VALUE_OPTIONAL, 'Path to SugarCRM Application'));
        $this->getDefinition()->addOption(new InputOption('--current_user', null, InputOption::VALUE_OPTIONAL, 'The current user id to run the script under'));

        $this->registerCommands();
    }

    /**
     *
     */
    private function registerCommands()
    {
        $this->_addCommands(
            dirname(dirname(dirname(dirname(__DIR__)))).'/',
            dirname(__DIR__).'/Command/'
        );
    }

    public function addCommands(array $commands)
    {
        parent::addCommands($commands);

        foreach ($commands as $command) {
            if ($command instanceof SugarAwareCommand) {
                $command->setSugar($this->sugar);
            }
        }
    }

    /**
     * @param array|\Symfony\Component\Console\Command\Command[] $prefix
     * @param $dir
     *
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
            }
        }
    }

    public function getSugar()
    {
        return $this->sugar;
    }

    /**
     * @param $prefix
     * @param $path
     *
     * @return array
     */
    private function getFiles($prefix, $path)
    {
        $commands = array();
        $finder = new Finder();
        $iterator = $finder
            ->files()
            ->name('*.php')
            ->in($path);

        foreach ($iterator as $file) {
            $file_path = $file->getRealpath();
            $class_name = str_replace($prefix, '', $file_path);
            $class_name = str_replace('/', '\\', rtrim($class_name, '.php'));

            $commands[] = $class_name;
        }

        return $commands;
    }
}
