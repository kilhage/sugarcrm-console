<?php

namespace DRI\SugarCRM\Console;

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
     * @var string
     */
    private $console_path;

    /**
     * @param string $app_path
     * @param string $console_path
     */
    public function __construct($app_path, $console_path)
    {
        $this->app_path = $app_path;
        $this->console_path = $console_path;
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        return $this->app_path;
    }

    /**
     * @return string
     */
    public function getConsolePath()
    {
        return $this->console_path;
    }

    /**
     * @param int $current_user_id
     */
    public function loadCurrentUser($current_user_id = null)
    {
        global $current_user;

        if (empty($current_user->id) && !empty($current_user_id)) {
            $current_user->retrieve($current_user_id);
        } elseif (empty($current_user->id)) {
            $current_user->getSystemUser();
        }

        $this->current_user = $current_user;
    }

    /**
     *
     */
    public function start()
    {
        Bootstrap::boot();

        require_once 'include/MVC/SugarApplication.php';

        global $current_module, $currentModule;
        $currentModule = $current_module = 'Administration';
        $_REQUEST['module'] = $_POST['module'] = $_GET['module'] = $current_module;
        $_REQUEST['action'] = $_POST['action'] = $_GET['action'] = 'repair';

        $this->app = new \SugarApplication();
        $this->app->startSession();
        $this->app->controller = \ControllerFactory::getController($current_module);
        $this->app->loadLanguages();
        $this->app->loadGlobals();
        $this->app->loadLicense();
    }
}
