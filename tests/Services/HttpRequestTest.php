<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Services;

use Austral\HttpBundle\Configuration\HttpConfiguration;
use Austral\HttpBundle\DependencyInjection\Configuration;
use Austral\HttpBundle\Services\HttpRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class HttpRequestTest extends TestCase
{
  public function testLanguageFallsBackToTheDefaultLanguage(): void
  {
    $httpRequest = $this->createHttpRequest(['default_language' => 'fr']);

    self::assertSame('fr', $httpRequest->getLanguage());
  }

  public function testLanguageSetOverridesTheDefaultLanguage(): void
  {
    $httpRequest = $this->createHttpRequest(['default_language' => 'fr']);

    $httpRequest->setLanguage('en');

    self::assertSame('en', $httpRequest->getLanguage());
  }

  public function testEnvironmentAndLanguagesComeFromTheConfiguration(): void
  {
    $httpRequest = $this->createHttpRequest([
      'languages' => ['fr', 'en'],
      'env' => ['current' => 'preprod', 'list' => ['prod', 'preprod']],
    ]);

    self::assertSame('preprod', $httpRequest->getDomainEnv());
    self::assertSame(['fr', 'en'], $httpRequest->getMultiLanguages());
  }

  public function testRequestAndCompressionAreStored(): void
  {
    $httpRequest = $this->createHttpRequest([]);
    $request = Request::create('/');

    self::assertNull($httpRequest->getRequest());
    self::assertFalse($httpRequest->getIsCompressionGzipEnabled());

    $httpRequest->setRequest($request)->setCompressionGzipEnabled(true);

    self::assertSame($request, $httpRequest->getRequest());
    self::assertTrue($httpRequest->getIsCompressionGzipEnabled());
  }

  /**
   * @param array<string, mixed> $config
   *
   * @return HttpRequest<object>
   */
  private function createHttpRequest(array $config): HttpRequest
  {
    return new HttpRequest(new HttpConfiguration(array_replace_recursive(new Configuration()->getConfigDefault(), $config)));
  }
}
