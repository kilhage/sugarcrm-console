<?php

namespace DRI\SugarCRM\Console\Command\Module\Fields;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class CheckDeclaredCommand extends ApplicationCommand
{

    protected function configure()
    {
        $this->setName("module:fields:check-declared")
            ->addArgument("preg", InputArgument::OPTIONAL, "Regular expression to field the modules checked")
            ->setDescription("Check that all fields are declared in a modules bean");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $beanList, $beanFiles;
        $preg = $input->getArgument("preg");

        if (empty($preg)) {
            $preg = '/.*/';
        }

        if (strpos($preg, "/") !== 0) {
            $preg = "/$preg/";
        }

        $modules = array_keys($beanList);
        foreach ($modules as $module)
        {
            if (!preg_match($preg, $module))
                continue;

            $bean = \BeanFactory::getBean($module);

            if (!($bean instanceof \SugarBean)) {
                continue;
            }

            $object_name = $bean->getObjectName();

            $refl = new \ReflectionClass(get_class($bean));
            $missing = array ();

            foreach ($bean->getFieldDefinitions() as $name => $def)
            {
                if (!$refl->hasProperty($name))
                {
                    $missing[] = "    public \$$name;";
                }
            }

            if (!empty($missing))
            {
                echo "\n* the $object_name bean are missing: \n" . implode("\n", $missing) . "\n";
            }
            else
            {
                echo "\n* the $object_name are fine!\n";
            }
        }
    }

}
