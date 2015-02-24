<?php

namespace DRI\SugarCRM\Console\Command\Permissions;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Language\LanguageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class SetCommand extends ApplicationCommand
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
        $this->setName("perm:set")
            ->addOption('dry', null, InputOption::VALUE_NONE)
            ->setDescription("Corrects the local file permissions");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->input = $input;

        $user = \SugarConfig::getInstance()->get("default_permissions.user");
        $group = \SugarConfig::getInstance()->get("default_permissions.group");

        $str = !empty($group) && !empty($user) ? "$user:$group" : (!empty($user) ? $user : null);

        if (empty($str)) {
            $output->writeln("<error>Missing user & group in config.php at default_permissions.user & default_permissions.group</error>");
        } else {
            $output->writeln("<info>Changing owner of files to $str</info>");

            $this->exec("chown -R $str .");
        }

        $output->writeln("<info>Changing file permissions to 0755</info>");
        $this->exec("chmod -R 0755 .");
    }

    /**
     * @param $comand
     * @param bool $dry
     */
    private function exec($comand)
    {
        $this->output->writeln("<info>>>> $comand</info>");

        if (!$this->input->getOption("dry")) {
            system($comand);
        }
    }

}
