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

use Austral\HttpBundle\Services\CspNonce;
use PHPUnit\Framework\TestCase;

/**
 * One nonce per request: the same for every call, a new one after kernel.reset.
 */
final class CspNonceTest extends TestCase
{
  public function testTheNonceIs128BitsInBase64(): void
  {
    $nonce = new CspNonce()->get();

    self::assertMatchesRegularExpression('#^[A-Za-z0-9+/]+={0,2}$#', $nonce);
    self::assertSame(CspNonce::LENGTH, \strlen((string) base64_decode($nonce, true)));
  }

  public function testTheNonceIsTheSameDuringARequest(): void
  {
    $cspNonce = new CspNonce();

    self::assertSame($cspNonce->get(), $cspNonce->get());
  }

  public function testResetGivesANewNonce(): void
  {
    $cspNonce = new CspNonce();
    $first = $cspNonce->get();
    $cspNonce->reset();

    self::assertNotSame($first, $cspNonce->get());
  }

  public function testTwoRequestsHaveTwoNonces(): void
  {
    self::assertNotSame(new CspNonce()->get(), new CspNonce()->get());
  }
}
