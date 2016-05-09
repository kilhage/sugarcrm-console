<?php

namespace DRI\SugarCRM\Console\Command\User;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sugarcrm\Sugarcrm\Security\Password\Hash;
/**
 * @author Oskar Hellgren
 */
class SetPasswordCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName('user:password')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->setDescription('Set a new password for user');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $do_salt = false;
        if (file_exists('sugar_version.php')) {
            require 'sugar_version.php';
            if (isset($sugar_version) && $sugar_version > "7.6") {
                $do_salt = true;
            }
        }

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $db = \DBManagerFactory::getInstance();
        $id = $db->getOne("SELECT id FROM users WHERE user_name = '$username' AND deleted = 0");

        if (!empty($id)) {
            if (!$do_salt) {
                $user_hash = md5($password);
            } else {
                $hash = Hash::getInstance();
                $user_hash = $hash->hash($password);

                if (!$hash->verify($password, $user_hash)) {
                    $user_hash = null;
                    $output->writeLn("<error>An error occurred while generating hash for password: $password</error>");
                }
            }

            if (!empty($user_hash)) {
                $res = $db->query("UPDATE users SET user_hash = '$user_hash' WHERE id = '$id' ");
                if ($res) {
                    $output->writeln("<comment>Success!</comment>");
                } else {
                    $output->writeLn("<error>An error occurred while writing to database</error>");
                }
            }
        } else {
            $output->writeLn("<error>Could not find user with username: $username</error>");
        }
    }

}
