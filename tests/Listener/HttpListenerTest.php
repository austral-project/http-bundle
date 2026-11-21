<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Listener;

use Austral\HttpBundle\Configuration\HttpConfiguration;
use Austral\HttpBundle\DependencyInjection\Configuration;
use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;
use Austral\HttpBundle\Listener\HttpListener;
use Austral\HttpBundle\Services\HttpRequest;
use Austral\HttpBundle\Tests\Fixtures\TestHttpEvent;
use Austral\ToolsBundle\Services\Debug;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class HttpListenerTest extends TestCase
{
  /**
   * @var list<string>
   */
  private array $dispatched = [];

  private EventDispatcher $dispatcher;

  /**
   * @var HttpRequest<object>
   */
  private HttpRequest $httpRequest;

  protected function setUp(): void
  {
    $this->dispatched = [];
    $this->dispatcher = new EventDispatcher();
    foreach ([HttpEventInterface::EVENT_AUSTRAL_HTTP_REQUEST_INITIALISE, HttpEventInterface::EVENT_AUSTRAL_HTTP_RESPONSE] as $eventName)
    {
      $this->dispatcher->addListener($eventName, function () use ($eventName): void {
        $this->dispatched[] = $eventName;
      });
    }
  }

  public function testInitialiseCopiesTheJsonBodyIntoTheRequestParameters(): void
  {
    $request = Request::create('/', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{"name":"Austral","tags":["a","b"]}');

    $this->createListener()->onRequestInitialise($this->createRequestEvent($request));

    self::assertSame('Austral', $request->request->get('name'));
    self::assertSame(['a', 'b'], $request->request->all('tags'));
    self::assertSame($request, $this->httpRequest->getRequest());
  }

  public function testInitialiseUsesTheLocaleOfTheRoute(): void
  {
    $request = Request::create('/fr/');
    $request->attributes->set('_locale', 'fr');

    $this->createListener(['default_language' => 'en'])->onRequestInitialise($this->createRequestEvent($request));

    self::assertSame('fr', $this->httpRequest->getLanguage());
    self::assertSame('fr', $request->getLocale());
  }

  public function testInitialiseUsesTheDefaultLanguageWithoutLocale(): void
  {
    $request = Request::create('/');

    $this->createListener(['default_language' => 'en'])->onRequestInitialise($this->createRequestEvent($request));

    self::assertSame('en', $request->getLocale());
  }

  #[DataProvider('compressionProvider')]
  public function testInitialiseReadsTheCompressionFromTheConfiguration(bool $enabled): void
  {
    $this->createListener(['compression_gzip' => ['default' => $enabled]])->onRequestInitialise($this->createRequestEvent(Request::create('/')));

    self::assertSame($enabled, $this->httpRequest->getIsCompressionGzipEnabled());
  }

  /**
   * @return iterable<string, array{bool}>
   */
  public static function compressionProvider(): iterable
  {
    yield 'enabled' => [true];
    yield 'disabled' => [false];
  }

  public function testInitialiseDispatchesTheHttpEventOfTheRoute(): void
  {
    $request = Request::create('/');
    $request->attributes->set('_austral_http_event', TestHttpEvent::class);

    $this->createListener()->onRequestInitialise($this->createRequestEvent($request));

    self::assertSame([HttpEventInterface::EVENT_AUSTRAL_HTTP_REQUEST_INITIALISE], $this->dispatched);
  }

  public function testInitialiseWithoutHttpEventDispatchesNothing(): void
  {
    $this->createListener()->onRequestInitialise($this->createRequestEvent(Request::create('/')));

    self::assertSame([], $this->dispatched);
  }

  public function testResponseIsCompressedWithGzipWhenTheClientAcceptsIt(): void
  {
    $listener = $this->createListener(['compression_gzip' => ['default' => true]]);
    $request = $this->createAustralRequest('gzip, deflate');
    $listener->onRequestInitialise($this->createRequestEvent($request));
    $response = new Response('<p>Austral</p>');

    $listener->onResponse($this->createResponseEvent($request, $response));

    self::assertSame('gzip', $response->headers->get('Content-encoding'));
    self::assertSame('<p>Austral</p>', gzdecode((string) $response->getContent()));
    self::assertContains(HttpEventInterface::EVENT_AUSTRAL_HTTP_RESPONSE, $this->dispatched);
  }

  public function testResponseIsCompressedWithDeflateWhenGzipIsNotAccepted(): void
  {
    $listener = $this->createListener(['compression_gzip' => ['default' => true]]);
    $request = $this->createAustralRequest('deflate');
    $listener->onRequestInitialise($this->createRequestEvent($request));
    $response = new Response('<p>Austral</p>');

    $listener->onResponse($this->createResponseEvent($request, $response));

    self::assertSame('deflate', $response->headers->get('Content-encoding'));
    self::assertSame('<p>Austral</p>', gzinflate((string) $response->getContent()));
  }

  public function testResponseIsNotCompressedWhenTheCompressionIsDisabled(): void
  {
    $listener = $this->createListener(['compression_gzip' => ['default' => false]]);
    $request = $this->createAustralRequest('gzip');
    $listener->onRequestInitialise($this->createRequestEvent($request));
    $response = new Response('<p>Austral</p>');

    $listener->onResponse($this->createResponseEvent($request, $response));

    self::assertNull($response->headers->get('Content-encoding'));
    self::assertSame('<p>Austral</p>', $response->getContent());
  }

  public function testRedirectResponseIsNotCompressed(): void
  {
    $listener = $this->createListener(['compression_gzip' => ['default' => true]]);
    $request = $this->createAustralRequest('gzip');
    $listener->onRequestInitialise($this->createRequestEvent($request));
    $response = new RedirectResponse('/target');

    $listener->onResponse($this->createResponseEvent($request, $response));

    self::assertNull($response->headers->get('Content-encoding'));
  }

  /**
   * @param array<string, mixed> $config
   *
   * @return HttpListener<object>
   */
  private function createListener(array $config = []): HttpListener
  {
    $configuration = new HttpConfiguration(array_replace_recursive(new Configuration()->getConfigDefault(), $config));
    $this->httpRequest = new HttpRequest($configuration);

    return new HttpListener(
      $this->dispatcher,
      $configuration,
      $this->httpRequest,
      $this->createMock(Debug::class)
    );
  }

  private function createAustralRequest(string $acceptEncoding): Request
  {
    $request = Request::create('/', 'GET', [], [], [], ['HTTP_ACCEPT_ENCODING' => $acceptEncoding]);
    $request->attributes->set('_austral_http_event', TestHttpEvent::class);

    return $request;
  }

  private function createRequestEvent(Request $request): RequestEvent
  {
    return new RequestEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST);
  }

  private function createResponseEvent(Request $request, Response $response): ResponseEvent
  {
    return new ResponseEvent($this->createMock(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST, $response);
  }
}
