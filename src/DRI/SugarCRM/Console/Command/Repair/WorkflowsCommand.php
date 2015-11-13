<?php

namespace DRI\SugarCRM\Console\Command\Repair;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Emil Kilhage
 */
class WorkflowsCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('repair:workflows')
            ->setDescription('Repairs the workflows files');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require_once('include/workflow/plugin_utils.php');

        $workflow_object = \BeanFactory::getBean('WorkFlow');

        $module_array = $workflow_object->get_module_array();

        foreach ($module_array as $key => $module) {
            $dir = "custom/modules/".$module."/workflow";
            if (file_exists($dir)) {
                $this->removeWorkflowDir($dir);
            }
        }

        $output->write($this->stripBr(translate('LBL_REBUILD_WORKFLOW_CACHE', 'Administration')).' ');

        $workflow_object->repair_workflow();

        $output->writeln($this->stripBr(translate('LBL_DONE', 'Administration')));

        $output->write($this->stripBr(translate('LBL_REBUILD_WORKFLOW_COMPILING', 'Administration')));

        build_plugin_list();

        $output->writeln($this->stripBr(translate('LBL_DONE', 'Administration')));
    }

    private function stripBr($string)
    {
        return preg_replace('/<BR>/i', "", $string);
    }

    /**
     * @param string $dir
     */
    private function removeWorkflowDir($dir) {
        if ($elements = glob($dir."/*")) {
            foreach($elements as $element) {
                is_dir($element) ? $this->removeWorkflowDir($element) : unlink($element);
            }
        }
    }
}
