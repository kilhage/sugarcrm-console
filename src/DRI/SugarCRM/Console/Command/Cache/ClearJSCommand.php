<?php

namespace DRI\SugarCRM\Console\Command\Cache;

use DRI\SugarCRM\Console\Application as Sugar;
use DRI\SugarCRM\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @author Richard Hoorn
 */
class ClearJSCommand extends Command implements Command\SugarAwareCommand
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Sugar
     */
    protected $sugar;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $this->filesystem = new Filesystem();
    }

    /**
     * @return Sugar
     */
    public function getSugar()
    {
        return $this->sugar;
    }

    /**
     * @param Sugar $sugar
     */
    public function setSugar(Sugar $sugar)
    {
        $this->sugar = $sugar;
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('cache:clearjs')
            ->addOption('dry', null, InputOption::VALUE_NONE, 'Only output the things that will be cleared')
            ->setDescription('Clears the js-cache');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearComponentsFiles();
        $this->clearJsGroupingFiles();
        $this->clearLessFiles();
    }

    /**
     *
     */
    private function clearComponentsFiles()
    {
        $this->output->writeln('Clearing components cache');

        $app_path = $this->getSugar()->getAppPath();

        $directory = sprintf('%s/cache/javascript/base', $app_path);

        if ($this->filesystem->exists($directory)) {
            $finder = new Finder();
            $files = $finder->files()
                ->name('components_*.js')
                ->in($directory);

            foreach ($files as $file) {
                $this->clearFileIfExists($file);
            }
        }
    }

    /**
     *
     */
    private function clearJsGroupingFiles()
    {
        $this->output->writeln('Clearing js grouping cache');

        $app_path = $this->getSugar()->getAppPath();

        $directory = sprintf('%s/cache/include/javascript', $app_path);

        if ($this->filesystem->exists($directory)) {
            $finder = new Finder();
            $files = $finder->files()
                ->name('sugar_*.js')
                ->in($directory);

            foreach ($files as $file) {
                $this->clearFileIfExists($file);
            }
        }
    }

    /**
     *
     */
    private function clearLessFiles()
    {
        $this->output->writeln('Clearing less cache');

        $app_path = $this->getSugar()->getAppPath();

        $directory = sprintf('%s/cache/themes/clients/base/default', $app_path);

        if ($this->filesystem->exists($directory)) {
            $finder = new Finder();
            $files = $finder->files()
                ->name('*.css')
                ->in($directory);

            foreach ($files as $file) {
                $this->clearFileIfExists($file);
            }
        }
    }
    
    /**
     * @param $file
     */
    private function clearFileIfExists($file)
    {
        if ($this->filesystem->exists($file)) {
            if ($this->input->getOption('verbose')) {
                $this->output->writeln(" - Removing file: $file");
            }

            if (!$this->input->getOption('dry')) {
                $this->filesystem->remove($file);
            }
        }
    }
}
