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

use Austral\EntityBundle\Entity\Entity;
use Austral\Contracts\Domain\FilterByDomainInterface;
use Austral\HttpBundle\Services\DomainsManagement;
use Doctrine\ORM\Mapping as ORM;

/**
 * Austral Translate Entity Social Network Trait.
 * @template T of object
 *
 * @phpstan-require-implements FilterByDomainInterface
 * @phpstan-require-extends Entity
 *
 *
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
   * @return string|null
   */
  public function getDomainId(): ?string
  {
    return $this->domainId;
  }

  /**
   * @param string|null $domainId
   *
   * @return $this
   */
  public function setDomainId(?string $domainId = DomainsManagement::DOMAIN_ID_MASTER): self
  {
    $this->domainId = $domainId;
    return $this;
  }

}
