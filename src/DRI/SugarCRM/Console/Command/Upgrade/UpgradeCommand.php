<?php

namespace DRI\SugarCRM\Console\Command\Upgrade;

use DRI\SugarCRM\Console\Application as Sugar;
use DRI\SugarCRM\Console\Command;
use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class UpgradeCommand extends Command implements Command\SugarAwareCommand
{
    /**
     * @var Sugar
     */
    private $sugar;

    /**
     *
     */
    protected function configure()
    {
        $this->setName('self-upgrade')
            ->setDescription('Upgrades the sugarcrm-console to the latest version');
    }

    /**
     * @return Sugar
     */
    public function getSugar()
    {
        return $this->sugar;
    }

    /**
     * @param Sugar $sugar
     */
    public function setSugar(Sugar $sugar)
    {
        $this->sugar = $sugar;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sugar = $this->getSugar();

        chdir($sugar->getConsolePath());

        system('./script/self-upgrade');
    }
}
