<?php

namespace DRI\SugarCRM\Console\Command\Cache;

use DRI\SugarCRM\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Emil Kilhage
 */
class WarmupCommand extends Command
{

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->filesystem = new Filesystem();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName("cache:warmup")
            ->addOption("dry", null, InputOption::VALUE_NONE, "Only output the things that will be created")
            ->setDescription("Warms up the cache");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Warming up cache");

        $directories = array (
            'cache',
            'cache/csv',
            'cache/feeds',
            'cache/images',
            'cache/import',
            'cache/layout',
            'cache/pdf',
            'cache/xml',
        );

        foreach ($directories as $directory) {
            if (!$this->filesystem->exists($directory)) {
                if ($this->input->getOption('verbose')) {
                    $this->output->writeln("creating directory: $directory");
                }

                if (!$this->input->getOption('dry')) {
                    $this->filesystem->mkdir($directory);
                }
            }
        }

        $output->writeln("Done");
    }

}
