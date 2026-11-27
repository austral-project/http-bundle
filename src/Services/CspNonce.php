<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Services;

use Symfony\Contracts\Service\ResetInterface;

/**
 * Austral CSP Nonce.
 *
 * Nonce of the Content Security Policy for the current request: one random value, the same for every inline
 * <style> and <script> of the response (and, with the CSP, for the header). It is created on first use and
 * forgotten by kernel.reset, so a new request gets a new nonce.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
final class CspNonce implements ResetInterface
{
  /**
   * Bytes of randomness of a nonce (128 bits, as recommended by the CSP specification).
   */
  public const int LENGTH = 16;

  /**
   * @var string|null Nonce of the current request, null until it is used
   */
  private ?string $nonce = null;

  /**
   * get
   * The nonce of the current request, encoded in base64.
   *
   * @return string
   *
   * @throws \Random\RandomException when no source of randomness is available
   */
  public function get(): string
  {
    return $this->nonce ??= base64_encode(random_bytes(self::LENGTH));
  }

  /**
   * reset
   * Forgets the nonce: the next request gets a new one.
   *
   * @return void
   */
  public function reset(): void
  {
    $this->nonce = null;
  }
}
