<?php

namespace Kilhage\SugarCRM;

/**
 * @author Emil Kilhage
 */
class Application
{

    private $sugar_path;
    private $current_user;
    private $app;
    private $db;

    public function init()
    {
        $include_path = $this->getSugarPath();

        set_include_path($include_path);
        chdir($include_path);

        require_once('include/entryPoint.php');
    }

    /**
     * @param mixed $sugar_path
     */
    public function setSugarPath($sugar_path)
    {
        $this->sugar_path = $sugar_path;
    }

    /**
     * @return mixed
     */
    public function getSugarPath()
    {
        if (empty($this->sugar_path)) {
            $include_path = $_SERVER['PWD'] . '/';

            while (!file_exists($include_path . 'sugar_version.php') && $include_path != '//')
            {
                if (file_exists($include_path . 'docroot/sugar_version.php'))
                {
                    $include_path .= 'docroot/';
                }
                else
                {
                    $include_path = dirname($include_path) . '/';
                }
            }

            if ($include_path == '//')
            {
                die("Could not find base path \n");
            }

            $this->sugar_path = rtrim($include_path, '/');
        }

        return $this->sugar_path;
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
