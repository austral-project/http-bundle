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

use Austral\HttpBundle\Configuration\HttpConfiguration;
use Austral\HttpBundle\DependencyInjection\AustralHttpExtension;
use Austral\HttpBundle\Listener\HttpListener;
use Austral\HttpBundle\Services\CspNonce;
use Austral\HttpBundle\Services\HttpRequest;
use Austral\HttpBundle\Twig\CspExtension;
use Austral\ToolsBundle\Services\Debug;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The services are defined explicitly: a missing or misnamed argument only shows up when the container
 * is compiled or the service is created. This test loads the extension, compiles the container and
 * creates every service of the bundle.
 */
final class ContainerTest extends TestCase
{
  /**
   * Public aliases: the supported way to get a service from the container.
   * austral.http.config stays public because some bundles still read it from the container.
   */
  private const array PUBLIC_ALIASES = [
    'austral.http.config',
    'austral.http.csp_nonce',
  ];

  /**
   * @return iterable<string, array{string, class-string}>
   */
  public static function serviceIds(): iterable
  {
    yield HttpListener::class => [HttpListener::class, HttpListener::class];
    yield HttpRequest::class => [HttpRequest::class, HttpRequest::class];
    yield HttpConfiguration::class => [HttpConfiguration::class, HttpConfiguration::class];
    yield CspNonce::class => [CspNonce::class, CspNonce::class];
    yield CspExtension::class => [CspExtension::class, CspExtension::class];
    yield 'austral.http.request' => ['austral.http.request', HttpRequest::class];
    yield 'austral.http.config' => ['austral.http.config', HttpConfiguration::class];
    yield 'austral.http.csp_nonce' => ['austral.http.csp_nonce', CspNonce::class];
  }

  /**
   * @param class-string $class
   */
  #[DataProvider('serviceIds')]
  public function testEveryServiceCanBeCreated(string $id, string $class): void
  {
    $container = $this->compiledContainer(publicServices: true);

    self::assertInstanceOf($class, $container->get($id));
  }

  public function testTheBundleDefinesNoOtherService(): void
  {
    $ids = [];
    foreach (self::loadExtension()->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\HttpBundle\\'))
      {
        $ids[] = $id;
      }
    }
    sort($ids);

    self::assertSame([HttpConfiguration::class, HttpListener::class, CspNonce::class, HttpRequest::class, CspExtension::class], $ids);
  }

  public function testOnlyTheAustralAliasesArePublic(): void
  {
    $container = self::loadExtension();

    foreach ($container->getDefinitions() as $id => $definition)
    {
      if (str_starts_with($id, 'Austral\\HttpBundle\\'))
      {
        self::assertFalse($definition->isPublic(), sprintf('The service "%s" must be private.', $id));
      }
    }
    $publicAliases = [];
    foreach ($container->getAliases() as $id => $alias)
    {
      if ($alias->isPublic())
      {
        $publicAliases[] = $id;
      }
    }
    sort($publicAliases);

    self::assertSame(self::PUBLIC_ALIASES, $publicAliases);
  }

  public function testPublicAliasesCanBeFetched(): void
  {
    $container = $this->compiledContainer(publicServices: false);

    foreach (self::PUBLIC_ALIASES as $alias)
    {
      self::assertTrue($container->has($alias), sprintf('The alias "%s" must be public.', $alias));
    }
    self::assertInstanceOf(HttpConfiguration::class, $container->get('austral.http.config'));
    self::assertInstanceOf(CspNonce::class, $container->get('austral.http.csp_nonce'));
    self::assertFalse($container->has('austral.http.request'), 'The alias "austral.http.request" must be private.');
  }

  public function testTheConfigurationReceivesTheBundleParameter(): void
  {
    $configuration = $this->compiledContainer(publicServices: true)->get(HttpConfiguration::class);

    self::assertInstanceOf(HttpConfiguration::class, $configuration);
    self::assertSame('https', $configuration->get('protocol'));
  }

  public function testTheListenerIsTaggedForEachKernelEvent(): void
  {
    $tags = self::loadExtension()->getDefinition(HttpListener::class)->getTag('kernel.event_listener');

    self::assertSame([
      ['event' => 'kernel.request', 'method' => 'onRequestInitialise', 'priority' => 30],
      ['event' => 'kernel.request', 'method' => 'onRequest'],
      ['event' => 'kernel.controller', 'method' => 'onController'],
      ['event' => 'kernel.response', 'method' => 'onResponse', 'priority' => -2048],
      ['event' => 'kernel.exception', 'method' => 'onException', 'priority' => 2048],
      ['event' => 'kernel.finish_request', 'method' => 'onFinishRequest'],
    ], $tags);
  }

  public function testTheServicesWithTheStateOfARequestAreReset(): void
  {
    $container = self::loadExtension();

    foreach ([HttpListener::class, HttpRequest::class, CspNonce::class] as $id)
    {
      self::assertSame([['method' => 'reset']], $container->getDefinition($id)->getTag('kernel.reset'), sprintf('"%s" must be reset between two requests.', $id));
    }
  }

  public function testTheCspExtensionIsATwigExtension(): void
  {
    self::assertTrue(self::loadExtension()->getDefinition(CspExtension::class)->hasTag('twig.extension'));
  }

  /**
   * The services that the bundle needs from the application (event dispatcher, tools-bundle)
   * are added as minimal definitions.
   */
  private static function loadExtension(): ContainerBuilder
  {
    $container = new ContainerBuilder();
    $container->setDefinition('event_dispatcher', new Definition(EventDispatcher::class));
    $container->setDefinition('austral.tools.debug', new Definition(Debug::class));
    new AustralHttpExtension()->load([], $container);

    return $container;
  }

  private function compiledContainer(bool $publicServices): ContainerBuilder
  {
    $container = self::loadExtension();
    if ($publicServices)
    {
      foreach ($container->getDefinitions() as $id => $definition)
      {
        if (str_starts_with($id, 'Austral\\HttpBundle\\'))
        {
          $definition->setPublic(true);
        }
      }
      $container->getAlias('austral.http.request')->setPublic(true);
    }
    $container->compile();

    return $container;
  }
}
