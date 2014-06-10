<?php

namespace DRI\SugarCRM\Console\Command;

use DRI\SugarCRM\Console\Application as Sugar;
use DRI\SugarCRM\Console\Command;
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

        $current_user_id = $input->getOption("current_user");
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
