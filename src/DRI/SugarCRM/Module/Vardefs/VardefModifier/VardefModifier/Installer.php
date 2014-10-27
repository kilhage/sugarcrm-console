<?php

require_once dirname(__FILE__) . '/Exception.php';

/**
 * @author Emil Kilhage
 */
class VardefModifier_Installer
{

    private static $help = "
****************************************************
Installs an empty yaml vardef addition for a module
****************************************************
\$ php install.php [[--core|--force|--help]] [[module(s)]]

    args:
        -c|--core: Install in module core instead of custom

        -f|--force: Force installation
                    be careful using this, is overwriting
                    files that there could have been modifications in

        -h|--help: Display help text

";

    public static function help()
    {
        return self::$help;
    }

    private $root_dir;
    private $modifier_dir;
    private $core = false;
    private $force = false;
    private $modules = array ();

    /**
     * @param array $args
     */
    public function __construct($root_dir, array $args)
    {
        $this->root_dir = $root_dir;
        $this->modifier_dir = str_replace("$this->root_dir/", '', dirname(dirname(__FILE__)));
        $this->parseArgs($args);
    }

    private function parseArgs(array $args)
    {
        foreach ($args as $arg)
        {
            if (strpos($arg, '-') === 0)
            {
                switch ($arg)
                {
                    case '--core':
                    case '-c':
                        $this->core = true;
                        break;
                    case '--force':
                    case '-f':
                        $this->force = true;
                        break;
                    case '--help':
                    case '-h':
                        throw new VardefModifier_Exception(self::help());
                    default:
                        throw new VardefModifier_Exception("Invalid flag: $args");
                        break;
                }
            }
            else
            {
                $this->modules[] = $arg;
            }
        }
    }

    public function install()
    {
        global $beanList;

        if (empty($this->modules))
        {
            throw new VardefModifier_Exception("Missing modules");
        }

        foreach ($this->modules as $module)
        {
            if (!isset($beanList[$module]))
                throw new VardefModifier_Exception("Invalid module: $module \n");

            echo "* Installing $module \n";
            $this->writePhpFile($module);
            $this->writeYamlFile($module);
        }
    }

    private function getYamlTemplate($module)
    {
        $file = file_get_contents(dirname(dirname(__FILE__)) . '/vardefs.template.yml');
        return str_replace('$module', $module, $file);
    }

    private function getPhpTemplate($module)
    {
        $class_name = __CLASS__;
        return <<<PHP
<?php

/* Installed by $class_name */
if (!isset(\$dictionary) || !is_array(\$dictionary))
    global \$dictionary;
{$this->getPhpCode($module)}
/* End installation */

PHP;
    }

    private function getPhpCode($module)
    {
        return <<<PHP
require_once '$this->modifier_dir/VardefModifier.php';
\$dictionary = VardefModifier::modify("$module", \$dictionary)->
    yaml("{$this->getYamlFilePath($module)}")->
    get();
PHP;
    }

    private function getPhpCoreVardefAddition($module)
    {
        $class_name = __CLASS__;
        return <<<PHP
/* Installed by $class_name */
{$this->getPhpCode($module)}
/* End installation */

PHP;
    }

    private function getYamlFilePath($module)
    {
        $dir = $this->core ? "modules/$module" : "custom/modules/$module";
        is_dir($dir) or mkdir($dir, 0755, true);
        return "$dir/vardefs.yml";
    }

    private function getPhpFilePath($module)
    {
        $dir = $this->core ?
            "modules/$module" :
            "custom/Extension/modules/$module/Ext/Vardefs";
        is_dir($dir) or mkdir($dir, 0755, true);
        return "$dir/" . ($this->core ? "vardefs.php" : "yaml_vardefs.php");
    }

    private function writePhpFile($module)
    {
        if (!$this->core)
        {
            $file_path = $this->getPhpFilePath($module);
            if ($this->force || !file_exists($file_path))
            {
                echo "Creating: $file_path \n";
                file_put_contents($file_path, $this->getPhpTemplate($module));
            }
            else
            {
                echo "$file_path does already exists, skipping... \n";
            }
        }
        else
        {
            $filename = $this->getPhpFilePath($module);
            $content = file_get_contents($filename);
            if ($this->force || strpos($content, "VardefModifier::modify(") === false)
            {
                echo "Installing in $filename \n";
                $content = rtrim($content, "?>\n");
                $content .= "\n\n";
                $content .= $this->getPhpCoreVardefAddition($module);
                file_put_contents($filename, $content);
            }
            else
            {
                echo "$filename is already installed, skipping... \n";
            }
        }
    }

    private function writeYamlFile($module)
    {
        $file_path = $this->getYamlFilePath($module);
        if ($this->force || !file_exists($file_path))
        {
            echo "Creating: $file_path \n";
            file_put_contents($file_path, $this->getYamlTemplate($module));
        }
        else
        {
            echo "$file_path does alredy exists, skipping... \n";
        }
    }

}
