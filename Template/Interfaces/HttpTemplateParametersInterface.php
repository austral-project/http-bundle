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
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpTemplateParametersInterface
{

  /**
   * @return array
   */
  public function __serialize();

  /**
   * @param string $key
   * @param $value
   * @param bool $merge
   *
   * @return $this
   */
  public function addParameters(string $key, $value, bool $merge = false): HttpTemplateParametersInterface;

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
   * @return array
   */
  public function getParameters(): array;

  /**
   * Get parameters
   *
   * @param string $key
   * @param null $default
   *
   * @return mixed
   */
  public function getParameter(string $key, $default = null);

  /**
   * @param array $parameters
   *
   * @return $this
   */
  public function setParameters(array $parameters): HttpTemplateParametersInterface;

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