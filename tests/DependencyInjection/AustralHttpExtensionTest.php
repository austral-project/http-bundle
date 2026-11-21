<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\DependencyInjection;

use Austral\HttpBundle\DependencyInjection\AustralHttpExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AustralHttpExtensionTest extends TestCase
{
  public function testLoadRegistersTheHttpServices(): void
  {
    $container = $this->load([]);

    self::assertTrue($container->hasDefinition('austral.http.listener'));
    self::assertTrue($container->hasDefinition('austral.http.request'));
    self::assertTrue($container->hasDefinition('austral.http.config'));
  }

  public function testLoadRegistersNoDomainService(): void
  {
    $container = $this->load([]);

    self::assertFalse($container->has('austral.http.domains.management'));
    self::assertFalse($container->has('austral.entity_manager.domain'));
    self::assertFalse($container->hasParameter('austral.entity.domain.class'));
  }

  public function testLoadUsesTheDefaultConfiguration(): void
  {
    $config = $this->load([])->getParameter('austral_http');

    self::assertIsArray($config);
    self::assertSame('https', $config['protocol']);
    self::assertSame('en', $config['default_language']);
    self::assertSame(['current' => 'prod', 'list' => ['prod']], $config['env']);
    self::assertSame(['default' => true], $config['compression_gzip']);
  }

  public function testLoadMergesTheApplicationConfiguration(): void
  {
    $config = $this->load([[
      'default_language' => 'fr',
      'languages' => ['fr', 'en'],
      'compression_gzip' => ['default' => false, 'admin' => true],
    ]])->getParameter('austral_http');

    self::assertIsArray($config);
    self::assertSame('fr', $config['default_language']);
    self::assertSame(['fr', 'en'], $config['languages']);
    self::assertSame(['default' => false, 'admin' => true], $config['compression_gzip']);
    self::assertSame('https', $config['protocol']);
  }

  /**
   * @param array<int, array<string, mixed>> $configs
   */
  private function load(array $configs): ContainerBuilder
  {
    $container = new ContainerBuilder();
    new AustralHttpExtension()->load($configs, $container);

    return $container;
  }
}
