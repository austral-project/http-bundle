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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use \Exception;

/**
 * Austral Http Extension.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class AustralHttpExtension extends Extension
{
  /**
   * {@inheritdoc}
   * @throws Exception
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $defaultConfig = $configuration->getConfigDefault();
    $config = array_replace_recursive($defaultConfig, $config);
    $container->setParameter('austral_http', $config);

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    $loader->load('services.yaml');
  }

}
