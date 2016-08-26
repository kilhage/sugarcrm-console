<?php

namespace DRI\SugarCRM\Console\Generator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This file is part of the DRI Sugar CRM Module Creator library
 *
 * Copyright (c) 2013 Emil Kilhage, DRI Nordic
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license MIT
 *   See LICENSE shipped with this library.
 */
class ModuleCreator
{
    /**
     * @var array
     */
    private static $arguments = array (
        'assignable' => false,
        'team_security' => false,
        'force' => false,
        'help' => false,
        'audited' => false,
        'dry' => false,
        'importable' => false,
        'template' => 'basic',
        'template_class_name' => 'Basic',
        'templates' => array ('basic'),
        'defined_fields' => array (),
        'object_name' => null,
        'module_name' => null,
        'table_name' => null,
        'object_name_auc' => null,
        'module_name_auc' => null,
    );

    /**
     *
     * @param array $args
     * @return array
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public static function parseArgs(array $args)
    {
        global $sugar_flavor;

        $arguments = array_merge(self::$arguments, $args);

        if (empty($arguments['module_name']))
        {
            $arguments['module_name'] = $arguments['object_name'] . 's';
        }

        if (empty($arguments['table_name']))
        {
            $arguments['table_name'] = strtolower($arguments['module_name']);
        }

        $arguments['object_name_auc'] = strtoupper($arguments['object_name']);
        $arguments['module_name_auc'] = strtoupper($arguments['module_name']);
        $arguments['template_class_name'] = ucfirst($arguments['template']);

        $arguments['team_security'] = !empty($arguments['team_security']) && $sugar_flavor !== 'CE';

        if ($arguments['template'] !== 'basic')
        {
            $arguments['templates'][] = $arguments['template'];
        }

        if ($arguments['team_security'])
        {
            $arguments['templates'][] = 'team_security';
        }

        if ($arguments['assignable'])
        {
            $arguments['templates'][] = 'assignable';
        }

        if (empty($arguments['translation_plural'])) {
            $arguments['translation_plural'] = self::translate($arguments['module_name']);
        }

        if (empty($arguments['translation_singular'])) {
            $arguments['translation_singular'] = self::translate($arguments['object_name']);
        }

        var_dump($arguments);

        return $arguments;
    }

    /**
     * @param string $module
     * @return string
     */
    public static function translate($module)
    {
        $module = str_replace('DRI_', '', $module);
        $module = preg_replace('/([a-z]{1})([A-Z]{1})/', '$1 $2', $module);
        return $module;
    }

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $templatePath;

    /**
     * ModuleCreator constructor.
     */
    public function __construct()
    {
        $this->templatePath = dirname(dirname((__DIR__))).'/Resources/tpls/new-module';
        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem("$this->templatePath"));
        $this->filesystem = new Filesystem();
    }

    /**
     * @global string $sugar_flavor
     * @param array $arguments
     */
    public function addModule(array $arguments = array ())
    {
        $arguments = self::parseArgs($arguments);

        foreach ($arguments['templates'] as $template) {
            \VardefManager::addTemplate(
                $arguments['module_name'],
                $arguments['object_name'],
                $template
            );
        }

        $arguments['defined_fields'] = array_keys($GLOBALS['dictionary'][$arguments['object_name']]['fields']);

        $files = $this->getFiles($this->templatePath, '/\.php/');

        foreach ($files as $from) {
            $to = $this->getPath($from, $arguments);
            $content = $this->twig->render($from, $arguments);
            $this->writeFile($to, $content, $arguments);
        }
    }

    /**
     * @param array $arguments
     */
    public function migrateModule(array $arguments = array ())
    {
        $arguments = self::parseArgs($arguments);

        $files = $this->getFiles($this->templatePath.'/modules/MODULE_NAME/clients', '/\.php/');

        foreach ($files as $from) {
            $to = $this->getPath($from, $arguments);
            $content = $this->twig->render($from, $arguments);
            $this->writeFile($to, $content, $arguments);
        }
    }

    /**
     * @param $file
     * @param $content
     * @param array $arguments
     *
     * @throws \Exception
     */
    private function writeFile($file, $content, array $arguments)
    {
        if ($this->filesystem->exists($file) && !$arguments['force']) {
            echo "$file already exists, skipping \n";
        } elseif (!$arguments['dry']) {
            $this->filesystem->dumpFile($file, $content);
            echo "$file has been created! \n";
        } else {
            echo "$file will be created! \n";
        }
    }

    /**
     * @param string $file
     * @param array $arguments
     * @return string
     */
    private function getPath($file, $arguments)
    {
        foreach ($arguments as $key => $value) {
            if (is_string($value)) {
                $file = str_replace(strtoupper($key), $value, $file);
            }
        }

        $file = str_replace($this->templatePath.'/', '', $file);

        return $file;
    }

    /**
     * Function to retrieve all file names of matching pattern in a directory (and it's subdirectories)
     * example: getFiles('./modules', '.+/EditView.php/'); // grabs all EditView.phps
     *
     * @param string $dir directory to look in [ USE ./ in front of the $dir! ]
     * @param string $pattern optional pattern to match against
     * @return array
     */
    private function getFiles($dir, $pattern = null)
    {
        $files = array ();

        if (!is_dir($dir)) {
            return array ();
        }

        $d = dir($dir);

        while ($e = $d->read())
        {
            if (0 === strpos($e, '.')) {
                continue;
            }

            $file = $dir . '/' . $e;
            if (is_dir($file)) {
                $files = array_merge($files, $this->getFiles($file, $pattern));
            } else {
                if (empty($pattern)) {
                    $files[] = $file;
                } elseif (preg_match($pattern, $file)) {
                    $files[] = $file;
                }
            }
        }

        foreach ($files as &$file) {
            $file = str_replace($this->templatePath.'/', '', $file);
        }

        return $files;
    }
}
