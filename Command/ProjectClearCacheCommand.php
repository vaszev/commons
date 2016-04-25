<?php

  namespace Vaszev\CommonsBundle\Command;

  use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;

  class ProjectClearCacheCommand extends ContainerAwareCommand {

    private $files = [];
    private $folders = [];



    protected function configure() {
      $this
          ->addUsage('i.e. project:clear:cache')
          ->setName('project:clear:cache')
          ->setDescription('Erase cache folder');
    }



    protected function execute(InputInterface $input, OutputInterface $output) {
      $container = $this->getContainer();
      $cacheDir = $container->get('kernel')->getRootDir() . '/../var/cache';
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
        $cnt = 0;
        foreach ($this->files as $file) {
          $cnt++;
          @unlink($file);
        }
        $output->writeln('<info>' . $cnt . " files was deleted from " . $cacheDir . '</info>');
      } else {
        $output->writeln('<info>' . "There is no files in " . $cacheDir . '</info>');
      }

      if (count($this->folders)) {
        $cnt = 0;
        for ($i = (count($this->folders) - 1); $i >= 0; $i--) {
          $ret = @rmdir($this->folders[$i]);
          if ($ret) {
            $cnt++;
          }
        }
        $output->writeln('<info>' . $cnt . " folders was deleted from " . $cacheDir . '</info>');
      } else {
        $output->writeln('<info>' . "There is no folders in " . $cacheDir . '</info>');
      }

    }



    private function chkDir($dir) {
      foreach (scandir($dir) as $file) {
        if ($file != "." && $file != "..") {
          if (is_dir($dir . '/' . $file)) {
            $this->folders[] = $dir . '/' . $file;
            $this->chkDir($dir . '/' . $file);
          } else {
            $this->files[] = $dir . '/' . $file;
          }
        }
      }
    }

  }
