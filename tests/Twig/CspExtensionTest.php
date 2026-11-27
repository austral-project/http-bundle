<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Tests\Twig;

use Austral\HttpBundle\Services\CspNonce;
use Austral\HttpBundle\Twig\CspExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * austral_csp_nonce() renders the nonce of the request in a template.
 */
final class CspExtensionTest extends TestCase
{
  public function testTheFunctionIsDeclared(): void
  {
    $names = array_map(static fn ($function) => $function->getName(), new CspExtension(new CspNonce())->getFunctions());

    self::assertSame(['austral_csp_nonce'], $names);
  }

  public function testTheTemplateRendersTheNonceOfTheRequest(): void
  {
    $cspNonce = new CspNonce();
    $twig = new Environment(new ArrayLoader([
      'page' => '<style nonce="{{ austral_csp_nonce() }}"></style><script nonce="{{ austral_csp_nonce() }}"></script>',
    ]));
    $twig->addExtension(new CspExtension($cspNonce));

    $nonce = $cspNonce->get();

    self::assertSame(\sprintf('<style nonce="%1$s"></style><script nonce="%1$s"></script>', $nonce), $twig->render('page'));
  }
}
