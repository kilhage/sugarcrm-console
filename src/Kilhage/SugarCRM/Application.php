<?php

namespace Kilhage\SugarCRM;

/**
 * @author Emil Kilhage
 */
class Application
{

    /**
     * @var \User
     */
    private $current_user;

    /**
     * @var \SugarApplication
     */
    private $app;

    /**
     * @var \DBManager
     */
    private $db;

    /**
     * @var string
     */
    private $app_path;

    /**
     * @param string $app_path
     */
    public function __construct($app_path)
    {
        $this->app_path = $app_path;
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        return $this->app_path;
    }

    /**
     * @param int $current_user_id
     */
    public function loadCurrentUser($current_user_id = 1)
    {
        global $current_user;

        if (empty($current_user->id)) {
            $current_user->retrieve($current_user_id);
        }

        $this->current_user = $current_user;
    }

    /**
     *
     */
    public function loadDatabase()
    {
        global $db;

        if (empty($db))
        {
            $db = \DBManagerFactory::getInstance();
        }

        $this->db = $db;
    }

    /**
     *
     */
    public function pauseTracker()
    {
        \TrackerManager::getInstance()->pause();
    }

    /**
     *
     */
    public function start()
    {
        require_once('include/MVC/SugarApplication.php');

        global $current_module, $currentModule;
        $currentModule = $current_module = 'Administration';
        $_REQUEST['module'] = $_POST['module'] = $_GET['module'] = $current_module;
        $_REQUEST['action'] = $_POST['action'] = $_GET['action'] = 'repair';

        $this->app = new \SugarApplication();
        $this->app->startSession();
        $this->app->controller = \ControllerFactory::getController($current_module);;
        $this->app->loadLanguages();
        $this->app->loadGlobals();
        $this->app->loadLicense();
    }

}
