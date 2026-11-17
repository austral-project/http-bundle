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
 * @template T of object
 *
 * @implements HttpTemplateParametersInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class HttpTemplateParametersParameters implements HttpTemplateParametersInterface
{

  /**
   * @var array<string, mixed>
   */
  protected array $parameters = array();

  /**
   * @var string|null
   */
  protected ?string $path = null;

  /**
   * @var string|null
   */
  protected ?string $name = "default";

  /**
   * @inheritDoc
   */
  public function __serialize()
  {
    return array_merge($this->getParameters(), array());
  }

  /**
   * @inheritDoc
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * @inheritDoc
   */
  public function setName(?string $name = null): self
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function addParameters(string $key, mixed $value, bool $merge = false): self
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
   * @inheritDoc
   */
  public function removeParameters(string $key): self
  {
    if(array_key_exists($key, $this->parameters))
    {
      unset($this->parameters[$key]);
    }
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function hasParameter(string $key): bool
  {
    return array_key_exists($key, $this->parameters);
  }

  /**
   * @inheritDoc
   */
  public function getParameters(): array
  {
    $parameters = $this->parameters;
    $parameters["template"] = array(
      "path"  =>  $this->path,
      "name"  =>  $this->name
    );
    return $parameters;
  }

  /**
   * @inheritDoc
   */
  public function getParameter(string $key, mixed $default = null): mixed
  {
    return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
  }

  /**
   * @inheritDoc
   */
  public function setParameters(array $parameters): HttpTemplateParametersParameters
  {
    $this->parameters = $parameters;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getPath(): ?string
  {
    return $this->path;
  }

  /**
   * @inheritDoc
   */
  public function setPath(?string $path): HttpTemplateParametersParameters
  {
    $this->path = $path;
    return $this;
  }

}