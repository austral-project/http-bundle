<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Template\Interfaces;


/**
 * Austral Http Template Interface.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpTemplateParametersInterface
{

  /**
   * @return array<string, mixed>
   */
  public function __serialize();

  /**
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * @param string|null $name
   * @return $this
   */
  public function setName(?string $name = null): self;

  /**
   * @param string $key
   * @param mixed $value
   * @param bool $merge
   *
   * @return $this
   */
  public function addParameters(string $key, mixed $value, bool $merge = false): self;

  /**
   * @param string $key
   *
   * @return $this
   */
  public function removeParameters(string $key): HttpTemplateParametersInterface;

  /**
   * @param string $key
   *
   * @return bool
   */
  public function hasParameter(string $key): bool;

  /**
   * Get parameters
   * @return array<string, mixed>
   */
  public function getParameters(): array;

  /**
   * Get parameters
   *
   * @param string $key
   * @param mixed $default
   *
   * @return mixed
   */
  public function getParameter(string $key, mixed $default = null): mixed;

  /**
   * @param array<string, mixed> $parameters
   *
   * @return $this
   */
  public function setParameters(array $parameters): self;

  /**
   * Get path
   * @return string|null
   */
  public function getPath(): ?string;

  /**
   * @param string|null $path
   *
   * @return $this
   */
  public function setPath(?string $path): HttpTemplateParametersInterface;

}