<?php

namespace Vaszev\CommonsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DocumentCommand extends ContainerAwareCommand {
  protected function configure() {
    $this
        ->addUsage('i.e. document:clear uploads/documents AppBundle:Document getUsed')
        ->setName('document:clear')
        ->setDescription('Clear unused document files from your document directory')
        ->addArgument(
            'path',
            InputArgument::REQUIRED,
            'What is the path of your document directory? (i.e. uploads/documents)'
        )
        ->addArgument(
            'repo-name',
            InputArgument::REQUIRED,
            'What is the repository\'s name? (i.e. AppBundle:Document)'
        )
        ->addArgument(
            'repo-method',
            InputArgument::REQUIRED,
            'What is repository method which collects used files to an array? (i.e. getUsed)'
        )
        ->addOption(
            'check',
            null,
            InputOption::VALUE_NONE,
            'If set, the task will only check for unnecessary files'
        );
  }



  protected function execute(InputInterface $input, OutputInterface $output) {
    $container = $this->getContainer();
    $container->get('doctrine')->getManager()->getFilters()->disable('not_deleted');
    $em = $container->get('doctrine')->getManager();
    $rootDir = $container->get('kernel')->getRootDir() . '/../web/';
    $path = $input->getArgument('path');
    $repoName = $input->getArgument('repo-name');
    $repoMethod = $input->getArgument('repo-method');
    $dir = $rootDir . $path;
    try {
      $repo = $em->getRepository($repoName);
      $used = $repo->$repoMethod();
      if (!is_array($used)) {
        throw new \Exception('Repository method has to return with an array (i.e. array(0=>"used-file.ext"))');
      }
      if (count($used)) {
        if (!isset($used[0]) || count($used[0]) != 1) {
          throw new \Exception('Repository method has to return with a sequential array (i.e. array(0=>"used-file.ext"))');
        }
      } else {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($repoName . ':Repository->' . $repoMethod . "() returned an empty list of used files. \nDo you confirm to delete all of your files in " . $path . " ? (yes/no) ", false);
        $answer = $helper->ask($input, $output, $question);
        if (!$answer) {
          throw new \Exception('User interrupted the process');
        }
      }
      $chk = @scandir($dir);
      if (!$chk) {
        throw new \Exception('Directory not found');
      }
    } catch (\Exception $e) {
      $output->writeln('<error>' . "Error: " . $e->getMessage() . '</error>');

      return -1;
    }
    $files = [];
    foreach (scandir($dir) as $file) {
      if ($file != "." && $file != "..") {
        if (!in_array($file, $used) && !is_dir($dir . '/' . $file)) {
          $files[] = $file;
        }
      }
    }
    if (count($files)) {
      $cnt = 0;
      foreach ($files as $file) {
        $cnt++;
        if ($input->getOption('check')) {
          $output->writeln($cnt . ". unused file: " . $file . " (only checked, not deleted)");
        } else {
          @unlink($dir . '/' . $file);
          $output->writeln($cnt . ". unused file: " . $file . " (deleted)");
        }
      }
    } else {
      $output->writeln('<info>' . "There is no unused files in " . $dir . '</info>');
    }
  }
}
