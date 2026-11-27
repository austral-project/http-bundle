<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Twig;

use Austral\HttpBundle\Services\CspNonce;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Austral CSP Twig Extension.
 *
 * austral_csp_nonce(): the nonce of the Content Security Policy for the attribute nonce of an inline <style> or
 * <script>, e.g. <style nonce="{{ austral_csp_nonce() }}">.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
final class CspExtension extends AbstractExtension
{
  /**
   * __construct
   *
   * @param CspNonce $cspNonce
   */
  public function __construct(
    private readonly CspNonce $cspNonce,
  ) {
  }

  /**
   * {@inheritDoc}
   */
  public function getFunctions(): array
  {
    return [
      new TwigFunction('austral_csp_nonce', $this->cspNonce->get(...)),
    ];
  }
}
