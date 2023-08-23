<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Austral Admin Configuration.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class Configuration implements ConfigurationInterface
{

  /**
   * {@inheritdoc}
   */
  public function getConfigTreeBuilder(): TreeBuilder
  {

    $treeBuilder = new TreeBuilder('austral_http');

    $rootNode = $treeBuilder->getRootNode();
    $node = $rootNode->children();
    $node->scalarNode("protocol")->end();
    $node->scalarNode("default_language")->end();
    $node->arrayNode("languages")
      ->scalarPrototype()->end()
    ->end();

    $node->arrayNode("env")
      ->children()
        ->scalarNode("current")->end()
        ->arrayNode("list")
          ->scalarPrototype()->end()
        ->end()
      ->end();
    $node->arrayNode("compression_gzip")
        ->children()
          ->booleanNode("admin")->end()
          ->booleanNode("website")->end()
          ->booleanNode("other")->end()
        ->end()
      ->end();
    return $treeBuilder;
  }


  /**
   * @return array
   */
  public function getConfigDefault(): array
  {
    return array(
      "protocol"            =>  "https",
      "default_language"    =>  "en",
      "languages"           =>  array(
      ),
      "env"                 =>  array(
        "current"   =>  "prod",
        "list"      =>  array(
          "prod"
        )
      ),
      "compression_gzip"    =>  array(
        "admin"   =>  true,
        "website" =>  true,
        "other"   =>  true,
      ),
    );
  }

}
