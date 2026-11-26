<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Handler;

use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Austral\HttpBundle\Tests\Fixtures\TestHttpHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class HttpHandlerTest extends TestCase
{
  private RequestStack $requestStack;

  protected function setUp(): void
  {
    $this->requestStack = new RequestStack();
  }

  public function testTheRequestIsReadFromTheRequestStackWhenItIsUsed(): void
  {
    $handler = $this->createHandler();
    self::assertNull($handler->getRequest());

    $first = Request::create('/first');
    $this->requestStack->push($first);
    self::assertSame($first, $handler->getRequest());

    $this->requestStack->pop();
    $second = Request::create('/second');
    $this->requestStack->push($second);
    self::assertSame($second, $handler->getRequest(), 'The handler must not keep the request of a previous call.');
  }

  public function testTheSessionIsTheSessionOfTheCurrentRequest(): void
  {
    $handler = $this->createHandler();
    $request = Request::create('/');
    $session = new Session(new MockArraySessionStorage());
    $request->setSession($session);
    $this->requestStack->push($request);

    self::assertSame($session, $handler->getSession());
  }

  public function testTheSessionIsNullWithoutSession(): void
  {
    $handler = $this->createHandler();
    $this->requestStack->push(Request::create('/'));

    self::assertNull($handler->getSession());
  }

  public function testAddFlashAddsTheMessageToTheSessionOfTheCurrentRequest(): void
  {
    $handler = $this->createHandler();
    $request = Request::create('/');
    $session = new Session(new MockArraySessionStorage());
    $request->setSession($session);
    $this->requestStack->push($request);

    $handler->addFlash('success', 'Saved');

    self::assertSame(['Saved'], $session->getFlashBag()->get('success'));
  }

  public function testAddFlashWithoutSessionThrowsALogicException(): void
  {
    $handler = $this->createHandler();
    $this->requestStack->push(Request::create('/'));

    $this->expectException(\LogicException::class);

    $handler->addFlash('success', 'Saved');
  }

  public function testResetForgetsTheStateOfTheRequest(): void
  {
    $handler = $this->createHandler();
    $handler->setTemplateParameters($this->createMock(HttpTemplateParametersInterface::class));
    $handler->handlerMethod = 'index';
    $handler->setRedirectUrl('/target');
    $handler->setRedirectStatus(302);

    $handler->reset();

    self::assertNull($handler->getTemplateParameters());
    self::assertNull($handler->handlerMethod);
    self::assertNull($handler->getRedirectUrl());
    self::assertSame(301, $handler->getRedirectStatus());
  }

  private function createHandler(): TestHttpHandler
  {
    return new TestHttpHandler(
      new EventDispatcher(),
      $this->createMock(TranslatorInterface::class),
      $this->createMock(RouterInterface::class),
      $this->requestStack,
      'test',
      null,
    );
  }
}
