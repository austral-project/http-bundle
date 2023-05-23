<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Template;

use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;

/**
 * Austral Template Parameters.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class HttpTemplateParametersParameters implements HttpTemplateParametersInterface
{

  /**
   * @var array
   */
  protected array $parameters = array();

  /**
   * @var string|null
   */
  protected ?string $path = null;


  public function __construct()
  {
  }

  /**
   * @return array
   */
  public function __serialize()
  {
    return array_merge($this->parameters, array());
  }

  /**
   * @param string $key
   * @param $value
   * @param bool $merge
   *
   * @return $this
   */
  public function addParameters(string $key, $value, bool $merge = false): HttpTemplateParametersParameters
  {
    if($merge && array_key_exists($key, $this->parameters))
    {
      $this->parameters[$key] = array_merge($this->parameters[$key], $value);
    }
    else
    {
      $this->parameters[$key] = $value;
    }
    return $this;
  }

  /**
   * @param string $key
   *
   * @return $this
   */
  public function removeParameters(string $key): HttpTemplateParametersParameters
  {
    if(array_key_exists($key, $this->parameters))
    {
      unset($this->parameters[$key]);
    }
    return $this;
  }

  /**
   * Get parameters
   *
   * @param string $key
   *
   * @return bool
   */
  public function hasParameter(string $key): bool
  {
    return array_key_exists($key, $this->parameters);
  }

  /**
   * Get parameters
   * @return array
   */
  public function getParameters(): array
  {
    return $this->parameters;
  }

  /**
   * Get parameters
   *
   * @param string $key
   * @param null $default
   *
   * @return mixed
   */
  public function getParameter(string $key, $default = null)
  {
    return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
  }

  /**
   * @param array $parameters
   *
   * @return $this
   */
  public function setParameters(array $parameters): HttpTemplateParametersParameters
  {
    $this->parameters = $parameters;
    return $this;
  }

  /**
   * Get path
   * @return string|null
   */
  public function getPath(): ?string
  {
    return $this->path;
  }

  /**
   * @param string|null $path
   *
   * @return $this
   */
  public function setPath(?string $path): HttpTemplateParametersParameters
  {
    $this->path = $path;
    return $this;
  }

}