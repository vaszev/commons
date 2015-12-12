<?php

  namespace Vaszev\CommonsBundle\Command;

  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;

  class ProjectSizeCommand extends ContainerAwareCommand {

    private $files = [];
    private $folders = [];
    private $groups = [];
    private $size = 0;



    protected function configure() {
      $this
          ->addUsage('i.e. project:size')
          ->setName('project:size')
          ->setDescription('Gathering information about project size');
    }



    protected function execute(InputInterface $input, OutputInterface $output) {
      $container = $this->getContainer();
      $cacheDir = $container->get('kernel')->getRootDir() . '/..';
      try {
        $chk = @scandir($cacheDir);
        if (!$chk) {
          throw new \Exception('Directory not found');
        }
      } catch (\Exception $e) {
        $output->writeln('<error>' . "Error: " . $e->getMessage() . '</error>');

        return -1;
      }
      $this->chkDir($cacheDir);

      if (count($this->files)) {
        $output->writeln('<info>' . count($this->files) . " files in " . count($this->folders) . " folders are take " . round($this->size / (1024 * 1024), 1) . " MB" . '</info>');
        $output->writeln('Directories:');
        foreach ($this->groups as $group => $size) {
          $output->writeln('<info>' . "- " . $group . " folder takes " . round($size / (1024 * 1024), 3) . " MB " . '</info>');
        }
      } else {
        $output->writeln('<info>' . "There is no files" . '</info>');
      }

    }



    private function chkDir($dir, $level = 0, $group = null) {
      $level++;
      foreach (scandir($dir) as $file) {
        if ($file != "." && $file != "..") {
          if (is_dir($dir . '/' . $file)) {
            $this->folders[] = $dir . '/' . $file;
            $this->chkDir($dir . '/' . $file, $level, (($level == 1) ? $file : $group));
          } else {
            $this->files[] = $dir . '/' . $file;
            $fileSize = filesize($dir . '/' . $file);
            $this->size += $fileSize;
            if (!empty($group)) {
              if (empty($this->groups[$group])) {
                $this->groups[$group] = 0;
              }
              $this->groups[$group] += $fileSize;
            }
          }
        }
      }
    }

  }
