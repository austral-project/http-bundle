<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Template;

use Austral\HttpBundle\Tests\Fixtures\TestTemplateParameters;
use PHPUnit\Framework\TestCase;

final class HttpTemplateParametersParametersTest extends TestCase
{
  public function testParametersIncludeTheTemplatePathAndName(): void
  {
    $parameters = new TestTemplateParameters();
    $parameters->setPath('@AustralWebsite/page.html.twig')->setName('page');
    $parameters->addParameters('title', 'Austral');

    self::assertSame([
      'title' => 'Austral',
      'template' => ['path' => '@AustralWebsite/page.html.twig', 'name' => 'page'],
    ], $parameters->getParameters());
  }

  public function testAddParametersMergesOnlyWhenAsked(): void
  {
    $parameters = new TestTemplateParameters();
    $parameters->addParameters('menu', ['home' => '/']);
    $parameters->addParameters('menu', ['contact' => '/contact'], true);

    self::assertSame(['home' => '/', 'contact' => '/contact'], $parameters->getParameter('menu'));

    $parameters->addParameters('menu', ['blog' => '/blog']);

    self::assertSame(['blog' => '/blog'], $parameters->getParameter('menu'));
  }

  public function testRemoveAndHasParameter(): void
  {
    $parameters = new TestTemplateParameters();
    $parameters->addParameters('title', 'Austral');

    self::assertTrue($parameters->hasParameter('title'));

    $parameters->removeParameters('title')->removeParameters('unknown');

    self::assertFalse($parameters->hasParameter('title'));
    self::assertSame('default', $parameters->getParameter('title', 'default'));
  }
}
