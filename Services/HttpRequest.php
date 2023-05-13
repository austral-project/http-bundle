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

/**
 * Austral Request.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class HttpRequest
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
   * @var array
   */
  protected array $multiLanguages = array();

  /**
   * @var Request|null
   */
  protected ?Request $request = null;

  /**
   * @var bool
   */
  protected bool $compressionGzipEnabled = false;

  /**
   * @param HttpConfiguration $httpConfiguration
   */
  public function __construct(HttpConfiguration $httpConfiguration)
  {
    $this->httpConfiguration = $httpConfiguration;
  }

  /**
   * @return string|null
   */
  public function getLanguage(): ?string
  {
    return $this->language ?: $this->httpConfiguration->get('default_language');
  }

  /**
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
   * @return array
   */
  public function getMultiLanguages(): array
  {
    return $this->httpConfiguration->get('languages', array());
  }

  /**
   * @return Request|null
   */
  public function getRequest(): ?Request
  {
    return $this->request;
  }

  /**
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
   * @return bool
   */
  public function getIsCompressionGzipEnabled(): bool
  {
    return $this->compressionGzipEnabled;
  }

  /**
   * @param bool $compressionGzipEnabled
   *
   * @return $this
   */
  public function setCompressionGzipEnabled(bool $compressionGzipEnabled): HttpRequest
  {
    $this->compressionGzipEnabled = $compressionGzipEnabled;
    return $this;
  }

}