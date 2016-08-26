<?php

namespace DRI\SugarCRM\Console\Command\Generate;

use DRI\SugarCRM\Console\Command\ApplicationCommand;
use DRI\SugarCRM\Console\Generator\ModuleCreator;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * @author Emil Kilhage
 */
class GenerateModuleCommand extends ApplicationCommand
{
    protected function configure()
    {
        $this->setName('generate:module')
            ->addArgument('object_name', InputArgument::OPTIONAL, 'The object name for the module (or the modules name in in singular), eg. Book.')
            ->addArgument('module_name', InputArgument::OPTIONAL, 'The module name for the module (or the modules name in in plural), eg. Books. If not supplied the module name will be the same as the object name but with a trailing \'s\'.')
            ->addArgument('table_name', InputArgument::OPTIONAL, 'The table name that the modules data will be stored in in the database, If not supplied, the ModuleName in lower case will be used as table name.')
            ->addOption('assignable', 'a', InputOption::VALUE_NONE, 'Implement the assignable sugar object, note that by default, this will not be added.')
            ->addOption('team_security', 'T', InputOption::VALUE_NONE, 'Implement the team_security sugar object, note that by default, this will not be added.')
            ->addOption('importable', 'I', InputOption::VALUE_NONE, 'The module should be importable.')
            ->addOption('audited', 'A', InputOption::VALUE_NONE, 'The module should be audited.')
            ->addOption('template', 't', InputOption::VALUE_REQUIRED, 'The template which the module should be inherited from. eg. person, company, issue, sale, file, basic.', 'basic')
            ->addOption('translation_plural', 'S', InputOption::VALUE_REQUIRED, 'Default module name translation in plural')
            ->addOption('translation_singular', 'P', InputOption::VALUE_REQUIRED, 'Default module name translation in singular')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'If the module already exists, the existing module will be overwritten.')
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'If you provide this argument, nothing will be written to the sugar application.')
            ->setDescription('Generators a new module');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $creator = new ModuleCreator();

        $params = array(
            'object_name' => $input->getArgument('object_name'),
            'module_name' => $input->getArgument('module_name'),
            'table_name' => $input->getArgument('table_name'),
            'assignable' => $input->getOption('assignable'),
            'translation_plural' => $input->getOption('translation_plural'),
            'translation_singular' => $input->getOption('translation_singular'),
            'team_security' => $input->getOption('team_security'),
            'importable' => $input->getOption('importable'),
            'audited' => $input->getOption('audited'),
            'force' => $input->getOption('force'),
            'dry' => $input->getOption('dry'),
            'template' => $input->getOption('template'),
        );

        if (!$input->getOption('no-interaction')) {
            $params = $this->wizard($params, $input, $output);
        }

        $creator->addModule($params);
    }

    protected function wizard(array $params, InputInterface $input, OutputInterface $output)
    {
        $helper = new QuestionHelper();

        if (empty($params['object_name'])) {
            $question = new Question('Object Name: ');
            $params['object_name'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['module_name'])) {
            $default = $params['object_name'] . 's';
            $question = new Question('Module Name (default: '.$default.'): ', $default);
            $params['module_name'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['table_name'])) {
            $default = strtolower($params['module_name']);
            $question = new Question('Table Name (default: '.$default.'): ', $default);
            $params['table_name'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['translation_singular'])) {
            $default = ModuleCreator::translate($params['object_name']);
            $question = new Question('Translation Singular (default: '.$default.'): ', $default);
            $params['translation_singular'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['translation_plural'])) {
            $default = ModuleCreator::translate($params['module_name']);
            $question = new Question('Translation Plural (default: '.$default.'): ', $default);
            $params['translation_plural'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['assignable'])) {
            $question = new ConfirmationQuestion('assignable (Y/n): ');
            $params['assignable'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['team_security'])) {
            $question = new ConfirmationQuestion('team_security (Y/n): ');
            $params['team_security'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['audited'])) {
            $question = new ConfirmationQuestion('audited (Y/n): ');
            $params['audited'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['importable'])) {
            $question = new ConfirmationQuestion('importable (Y/n): ');
            $params['importable'] = $helper->ask($input, $output, $question);
        }

        if (empty($params['template'])) {
            $question = new Question('template (default: basic): ', 'basic');
            $params['template'] = $helper->ask($input, $output, $question);
        }

        return $params;
    }
}
