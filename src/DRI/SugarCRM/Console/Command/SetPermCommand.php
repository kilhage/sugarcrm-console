<?php

namespace DRI\SugarCRM\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class SetPermCommand extends ApplicationCommand
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     *
     */
    protected function configure()
    {
        $this->setName('perm:set')
            ->addOption('dry', null, InputOption::VALUE_NONE)
            ->addOption('file-mode', null, InputOption::VALUE_REQUIRED, '', 755)
            ->setDescription('Corrects the local file permissions');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $path = is_dir(dirname(SUGAR_PATH).'/docroot') ? dirname(SUGAR_PATH) : SUGAR_PATH;
        $docroot = is_dir("$path/docroot") ? "$path/docroot" : $path;

        $this->exec("find $path -type f -exec chmod 644 {} \\;");

        if (is_dir("$path/bin")) {
            $this->exec("find $path/bin -exec chmod +x {} \\;");
        }

        if (is_dir("$path/scripts")) {
            $this->exec("find $path/scripts -exec chmod +x {} \\;");
        }

        $this->exec("find $path -type d -exec chmod 755 {} \\;");
    }

    /**
     * @param $command
     */
    private function exec($command)
    {
        $this->output->writeln("<info>>>> $command</info>");

        if (!$this->input->getOption('dry')) {
            system("$command");
        }
    }
}
