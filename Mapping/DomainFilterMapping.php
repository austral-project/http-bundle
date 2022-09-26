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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Mapping\EntityClassMapping;
use Austral\EntityBundle\Mapping\FieldMappingInterface;

/**
 * Austral DomainFilterMapping.
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
   * @return DomainFilterMapping
   */
  public function setAutoDomainId(bool $autoDomainId): DomainFilterMapping
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
   * @return DomainFilterMapping
   */
  public function setForAllDomainEnabled(bool $forAllDomainEnabled): DomainFilterMapping
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
  public function setAutoAttachement(bool $autoAttachement): DomainFilterMapping
  {
    $this->autoAttachement = $autoAttachement;
    return $this;
  }

  /**
   * @param EntityInterface $object
   * @param string|null $fieldname
   *
   * @return mixed
   * @throws \Exception
   */
  public function getObjectValue(EntityInterface $object, string $fieldname = null)
  {
    return $object->getValueByFieldname($fieldname);
  }

  /**
   * @param EntityInterface $object
   * @param string|null $fieldname
   * @param null $value
   *
   * @return $this
   * @throws \Exception
   */
  public function setObjectValue(EntityInterface $object, string $fieldname = null, $value = null): DomainFilterMapping
  {
    $object->setValueByFieldname($fieldname);
    return $this;
  }

}
