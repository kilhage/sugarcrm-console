<?php

namespace DRI\SugarCRM\Console\Command\Module\VardefModifier;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Emil Kilhage
 */
class DumpCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('module:vardef-modifier:dump')
            ->addArgument('moduleName', InputArgument::REQUIRED, '')
            ->addArgument('fileName', InputArgument::REQUIRED, '')
            ->addOption('targetFileName', null, InputOption::VALUE_REQUIRED, '', 'dri-fields')
            ->setDescription('');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $beanList;

        $moduleName = $input->getArgument('moduleName');
        $fileName = $input->getArgument('fileName');
        $targetFileName = $input->getOption('targetFileName');

        require_once 'custom/include/VardefModifier/VardefModifier.php';
        $vm = \VardefModifier::modify($moduleName, array());
        $vm->yaml($fileName);

        $objectName = $beanList[$moduleName];

        $dictionaryKey = $vm->getDictionaryKey();

        $dic = $vm->get();

        $templatePath = dirname(dirname(dirname(dirname((__DIR__)))));

        $fs = new Filesystem();
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem("$templatePath/Resources/tpls"));

        $definitions = array(
            'fields' => isset($dic[$dictionaryKey]['fields']) ? $dic[$dictionaryKey]['fields'] : array(),
            'relationships' => isset($dic[$dictionaryKey]['relationships']) ? $dic[$dictionaryKey]['relationships'] : array(),
            'indices' => isset($dic[$dictionaryKey]['indices']) ? $dic[$dictionaryKey]['indices'] : array(),
        );

        $arguments = array(
            'dictionaryKey' => $dictionaryKey,
            'objectName' => $objectName,
            'moduleName' => $moduleName,
            'fileName' => $fileName,
            'fields' => array(),
            'relationships' => array(),
            'indices' => array(),
        );

        foreach ($definitions as $type => $sub) {
            foreach ($sub as $name => $def) {
                $arguments[$type][$name] = array(
                    'name' => $name,
                    'def' => var_export($def, true),
                );
            }
        }

        if (empty($arguments['fields']) && empty($arguments['relationships']) && empty($arguments['indices'])) {
            return;
        }

        $targetFilePath = "custom/Extension/modules/$moduleName/Ext/Vardefs/$targetFileName.php";

        $output->writeln("Writing vardef to $targetFilePath");

        $content = $twig->render('module/vardefs/vardef.php.twig', $arguments);

        $content = trim($content)."\n";

        $fs->dumpFile($targetFilePath, $content);
    }
}
