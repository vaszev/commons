<?php

namespace Vaszev\CommonsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface {

  private $defaultImage = null;
  private $docs = null;
  private $imageQuality = 70;
  private $imageVariations = [];



  /**
   * {@inheritdoc}
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('vaszev_commons');

    $rootNode
        ->children()
        ->variableNode('default_image')->defaultValue($this->defaultImage)->end()
        ->variableNode('docs')->defaultValue($this->docs)->end()
        ->variableNode('image_quality')->defaultValue($this->imageQuality)->end()
        ->variableNode('image_variations')->defaultValue($this->imageVariations)->end()
        ->end();

    // Here you should define the parameters that are allowed to
    // configure your bundle. See the documentation linked above for
    // more information on that topic.

    return $treeBuilder;
  }
}
