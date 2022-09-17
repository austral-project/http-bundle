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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\EntityBundle\Entity\Interfaces\TranslateChildInterface;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Austral\EntityBundle\Entity\Interfaces\FilterByDomainInterface;
use Austral\HttpBundle\EntityManager\DomainEntityManager;
use Austral\ToolsBundle\AustralTools;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Austral Domain Service.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class DomainsManagement
{

  /**
   * @var DomainEntityManager
   */
  protected DomainEntityManager $domainEntityManager;

  /**
   * @var HttpRequest
   */
  protected HttpRequest $httpRequest;

  /**
   * @var ?string
   */
  protected ?string $host = null;

  /**
   * @var array
   */
  protected array $domains = array();

  /**
   * @var array
   */
  protected array $domainsById = array();

  /**
   * @var DomainInterface|null
   */
  protected ?DomainInterface $currentDomain = null;

  /**
   * @var DomainInterface|null
   */
  protected ?DomainInterface $domainMaster = null;

  /**
   * @var string|null
   */
  protected ?string $filterDomainId = null;

  /**
   * @var int
   */
  protected int $enabledDomainWithoutVirtual = 0;

  /**
   * Domains Service constructor.
   *
   * @param RequestStack $requestStack
   * @param DomainEntityManager $domainEntityManager
   * @param HttpRequest $httpRequest
   */
  public function __construct(RequestStack $requestStack, DomainEntityManager $domainEntityManager, HttpRequest $httpRequest)
  {
    $request = $requestStack->getCurrentRequest();
    $this->host = $request ? $request->getHost() : null;
    $this->domainEntityManager = $domainEntityManager;
    $this->httpRequest = $httpRequest;
  }

  /**
   * @return $this
   * @throws QueryException
   */
  public function initialize(): DomainsManagement
  {
    if(!$this->domains)
    {
      $this->domains = $this->domainEntityManager->selectAllEnabledDomains();

      /** @var DomainInterface $domain */
      foreach($this->domains as $domain)
      {
        $this->domainsById[$domain->getId()] = $domain;
        if($this->host === $domain->getDomain())
        {
          $this->currentDomain = $domain;
        }
        if(!$this->domainMaster && $domain->getIsMaster())
        {
          $this->domainMaster = $domain;
        }
        if(!$domain->getIsVirtual())
        {
          $this->enabledDomainWithoutVirtual++;
        }
      }
    }
    return $this;
  }

  /**
   * @return int
   */
  public function getEnabledDomainWithoutVirtual(): int
  {
    return $this->enabledDomainWithoutVirtual;
  }

  /**
   * @return array
   */
  public function getDomains(): array
  {
    return $this->domains;
  }

  /**
   * @return DomainInterface|null
   */
  public function getDomainMaster(): ?DomainInterface
  {
    return $this->domainMaster;
  }

  /**
   * @return DomainInterface|null
   */
  public function getCurrentDomain(): ?DomainInterface
  {
    return $this->currentDomain;
  }

  /**
   * @param DomainInterface $currentDomain
   *
   * @return $this
   */
  public function setCurrentDomain(DomainInterface $currentDomain): DomainsManagement
  {
    $this->currentDomain = $currentDomain;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getCurrentLanguage(): ?string
  {
    return $this->getCurrentDomain() ? ($this->getCurrentDomain()->getLanguage() ? : $this->httpRequest->getLanguage()) : $this->httpRequest->getLanguage();
  }

  /**
   * @param string $id
   *
   * @return ?DomainInterface
   */
  public function getDomainById(string $id): ?DomainInterface
  {
    return array_key_exists($id, $this->domainsById) ? $this->domainsById[$id] : null;
  }

  /**
   * @return string|null
   */
  public function getFilterDomainId(): ?string
  {
    return $this->filterDomainId;
  }

  /**
   * @param string|null $filterDomainId
   *
   * @return DomainsManagement
   */
  public function setFilterDomainId(?string $filterDomainId): DomainsManagement
  {
    $this->filterDomainId = $filterDomainId;
    return $this;
  }

  /**
   * @return ?DomainInterface
   */
  public function getFilterDomain(): ?DomainInterface
  {
    return $this->getDomainById($this->filterDomainId);
  }

  /**
   * @var array
   */
  protected array $objectsDomainAttachement = array();

  /**
   * @return array
   */
  public function getObjectsDomainAttachement(): array
  {
    return $this->objectsDomainAttachement;
  }

  /**
   * @param EntityInterface $object
   * @param bool $withChild
   *
   * @return $this
   */
  public function objectDomainAttachement(EntityInterface $object, bool $withChild = true): DomainsManagement
  {
    if($object instanceof TranslateChildInterface)
    {
      $object = $object->getMaster();
    }

    if($object instanceof FilterByDomainInterface && $this->filterDomainId)
    {
      $object->setDomainId($this->filterDomainId);
      if($withChild)
      {
        if(method_exists($object, "getChildren"))
        {
          foreach($object->getChildren() as $child)
          {
            $this->objectDomainAttachement($child);
          }
        }
        if(method_exists($object, "getChildrenEntities"))
        {
          foreach($object->getChildrenEntities() as $child)
          {
            $this->objectDomainAttachement($child);
          }
        }
      }
      $this->objectsDomainAttachement[] = $object;
    }
    return $this;
  }




}