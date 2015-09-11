<?php

namespace DRI\SugarCRM\Console\Command;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class SetOwnerCommand extends ApplicationCommand
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
        $this->setName('owner:set')
            ->addOption('dry', null, InputOption::VALUE_NONE)
            ->setDescription('Corrects the local file owner permissions');
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

        $user = \SugarConfig::getInstance()->get('default_permissions.user');
        $group = \SugarConfig::getInstance()->get('default_permissions.group');

        $str = !empty($group) && !empty($user) ? "$user:$group" : (!empty($user) ? $user : null);

        $path = is_dir(dirname(SUGAR_BASE_DIR).'/docroot') ? dirname(SUGAR_BASE_DIR) : SUGAR_BASE_DIR;

        if (empty($str)) {

            $message = <<<TXT
Add the following settings to the config_override.php, just make sure to change the group & owner to the right ones!

\$sugar_config['default_permissions']['user'] = 'apache';
\$sugar_config['default_permissions']['group'] = 'apache';

TXT;

            $output->writeln("<error>$message</error>");
        } else {
            $output->writeln("<info>Changing owner of files to $str</info>");

            $this->exec("chown -R $str $path");
        }
    }

    /**
     * @param $command
     */
    private function exec($command)
    {
        $this->output->writeln("<info>>>> $command</info>");

        if (!$this->input->getOption('dry')) {
            system($command);
        }
    }
}
