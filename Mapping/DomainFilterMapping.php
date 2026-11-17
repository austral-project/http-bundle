<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Mapping;

use Austral\EntityBundle\Mapping\EntityClassMapping;

/**
 * Austral DomainFilterMapping.
 * @template T of object
 *
 * @extends EntityClassMapping<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
final Class DomainFilterMapping extends EntityClassMapping
{

  /**
   * @var bool
   */
  protected bool $autoDomainId = false;

  /**
   * @var bool
   */
  protected bool $forAllDomainEnabled = false;

  /**
   * @var bool
   */
  protected bool $autoAttachement = false;

  /**
   * Constructor.
   */
  public function __construct()
  {
  }

  /**
   * @return bool
   */
  public function getAutoDomainId(): bool
  {
    return $this->autoDomainId;
  }

  /**
   * @param bool $autoDomainId
   *
   * @return $this
   */
  public function setAutoDomainId(bool $autoDomainId): self
  {
    $this->autoDomainId = $autoDomainId;
    return $this;
  }

  /**
   * @return bool
   */
  public function getForAllDomainEnabled(): bool
  {
    return $this->forAllDomainEnabled;
  }

  /**
   * @param bool $forAllDomainEnabled
   *
   * @return $this
   */
  public function setForAllDomainEnabled(bool $forAllDomainEnabled): self
  {
    $this->forAllDomainEnabled = $forAllDomainEnabled;
    return $this;
  }

  /**
   * @return bool
   */
  public function getAutoAttachement(): bool
  {
    return $this->autoAttachement;
  }

  /**
   * @param bool $autoAttachement
   *
   * @return $this
   */
  public function setAutoAttachement(bool $autoAttachement): self
  {
    $this->autoAttachement = $autoAttachement;
    return $this;
  }

}
