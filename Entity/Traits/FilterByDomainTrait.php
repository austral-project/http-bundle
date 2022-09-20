<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Entity\Traits;

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Interfaces\FilterByDomainInterface;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Translate Entity Social Network Trait.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
trait FilterByDomainTrait
{

  /**
   * @var string|null
   * @ORM\Column(name="domain_id", type="string", length=255, nullable=true )
   */
  protected ?string $domainId = null;

  /**
   * @var bool
   */
  protected bool $withoutDomainId = false;

  /**
   * @var DomainInterface|null
   */
  protected ?DomainInterface $domain = null;

  /**
   * @return string|null
   */
  public function getDomainId(): ?string
  {
    if($this->withoutDomainId)
    {
      return $this->domainId ?? "current";
    }
    return $this->domainId ?? null;
  }

  /**
   * @return string|null
   */
  public function getDomainIdReel(): ?string
  {
    return $this->domainId;
  }

  /**
   * @param string|null $domainId
   *
   * @return FilterByDomainInterface
   */
  public function setDomainId(?string $domainId = null): FilterByDomainInterface
  {
    if($domainId === "current")
    {
      $this->withoutDomainId = true;
    }
    $this->domainId = $domainId === "current" ? null : $domainId;
    return $this;
  }

  /**
   * @return DomainInterface|EntityInterface|null
   */
  public function getDomain(): ?EntityInterface
  {
    return $this->domain;
  }

  /**
   * @param DomainInterface|EntityInterface|null $domain
   *
   * @return FilterByDomainInterface|null
   */
  public function setDomain(?EntityInterface $domain): ?FilterByDomainInterface
  {
    if($domain) {
      $this->setDomainId($domain->getId());
      $this->domain = $domain;
    }
    return $this;
  }

}
