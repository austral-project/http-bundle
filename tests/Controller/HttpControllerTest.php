<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Controller;

use Austral\HttpBundle\Tests\Fixtures\TestHttpController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class HttpControllerTest extends TestCase
{
  public function testJsonEncodesTheData(): void
  {
    $response = $this->createController()->callJson(['status' => 'ok'], 201, ['X-Austral' => 'yes']);

    self::assertSame('{"status":"ok"}', $response->getContent());
    self::assertSame(201, $response->getStatusCode());
    self::assertSame('yes', $response->headers->get('X-Austral'));
  }

  public function testJsonKeepsAnAlreadyEncodedString(): void
  {
    $response = $this->createController()->callJson('{"status":"ok"}', json: true);

    self::assertSame('{"status":"ok"}', $response->getContent());
  }

  private function createController(): TestHttpController
  {
    return new TestHttpController(
      $this->createMock(ContainerInterface::class),
      $this->createMock(RouterInterface::class),
      $this->createMock(Environment::class)
    );
  }
}
