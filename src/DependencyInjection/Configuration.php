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
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
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
    $children = $rootNode->children();

    $children->scalarNode('protocol')->end();
    $children->scalarNode('default_language')->end();
    $children->arrayNode('languages')->scalarPrototype()->end();

    $envChildren = $children->arrayNode('env')->children();
    $envChildren->scalarNode('current')->end();
    $envChildren->arrayNode('list')->scalarPrototype()->end();
    $children->arrayNode('compression_gzip')->scalarPrototype()->end();

    return $treeBuilder;
  }

  /**
   * @return array<string, mixed>
   */
  public function getConfigDefault(): array
  {
    return [
      'protocol' => 'https',
      'default_language' => 'en',
      'languages' => [],
      'env' => [
        'current' => 'prod',
        'list' => [
          'prod',
        ],
      ],
      'compression_gzip' => [
        'default' => true,
      ],
    ];
  }
}
