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

use Austral\HttpBundle\Configuration\HttpConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Austral Request.
 *
 * State of the current main request (request, language, compression), set by the HttpListener and reset at the
 * start of each main request and by kernel.reset.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class HttpRequest implements ResetInterface
{
  /**
   * @var HttpConfiguration
   */
  protected HttpConfiguration $httpConfiguration;

  /**
   * @var string|null
   */
  protected ?string $language = null;

  /**
   * @var array<string, string>
   */
  protected array $multiLanguages = [];

  /**
   * @var Request|null
   */
  protected ?Request $request = null;

  /**
   * @var bool
   */
  protected bool $compressionGzipEnabled = false;

  /**
   * __construct
   *
   * @param HttpConfiguration $httpConfiguration
   *
   * @return void
   */
  public function __construct(HttpConfiguration $httpConfiguration)
  {
    $this->httpConfiguration = $httpConfiguration;
  }

  /**
   * getLanguage
   *
   * @return string
   */
  public function getLanguage(): string
  {
    return $this->language ?: $this->httpConfiguration->get('default_language');
  }

  /**
   * getDomainEnv
   *
   * @return string|null
   */
  public function getDomainEnv(): ?string
  {
    return $this->httpConfiguration->get('env.current');
  }

  /**
   * setLanguage
   *
   * @param string|null $language
   *
   * @return $this
   */
  public function setLanguage(?string $language): HttpRequest
  {
    $this->language = $language;

    return $this;
  }

  /**
   * getMultiLanguages
   *
   * @return array<string, string>
   */
  public function getMultiLanguages(): array
  {
    return $this->httpConfiguration->get('languages', []);
  }

  /**
   * getRequest
   *
   * @return Request|null
   */
  public function getRequest(): ?Request
  {
    return $this->request;
  }

  /**
   * setRequest
   *
   * @param Request|null $request
   *
   * @return $this
   */
  public function setRequest(?Request $request): HttpRequest
  {
    $this->request = $request;

    return $this;
  }

  /**
   * getIsCompressionGzipEnabled
   *
   * @return bool
   */
  public function getIsCompressionGzipEnabled(): bool
  {
    return $this->compressionGzipEnabled;
  }

  /**
   * setCompressionGzipEnabled
   *
   * @param bool $compressionGzipEnabled
   *
   * @return $this
   */
  public function setCompressionGzipEnabled(bool $compressionGzipEnabled): HttpRequest
  {
    $this->compressionGzipEnabled = $compressionGzipEnabled;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function reset(): void
  {
    $this->language = null;
    $this->request = null;
    $this->compressionGzipEnabled = false;
  }
}
