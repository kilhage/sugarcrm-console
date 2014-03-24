<?php

namespace Kilhage\SugarCRM\Command;

use Kilhage\SugarCRM\Application as Sugar;
use Kilhage\SugarCRM\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
abstract class ApplicationCommand extends Command implements SugarAwareCommand
{

    /**
     * @var Sugar
     */
    protected $sugar;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $sugar = $this->getSugar();

        $sugar_path = $input->getOption("sugar_path");
        if (!empty($sugar_path)) {
            $sugar_path = realpath($sugar_path);
            $sugar->setSugarPath($sugar_path);
        }

        $sugar->init();

        $current_user_id = $this->getOption("current_user");
        if (!empty($current_user_id)) {
            $sugar->loadCurrentUser($current_user_id);
        } else {
            $sugar->loadCurrentUser();
        }

        $sugar->loadDatabase();
        $sugar->pauseTracker();
        $sugar->start();
    }

    /**
     * @param Sugar $sugar
     */
    public function setSugar(Sugar $sugar)
    {
        $this->sugar = $sugar;
    }

    /**
     * @return Sugar
     */
    public function getSugar()
    {
        return $this->sugar;
    }

}
