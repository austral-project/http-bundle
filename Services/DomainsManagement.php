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

use Austral\Contracts\Entity\EntityInterface;
use Austral\Contracts\Domain\FilterByDomainInterface;
use Austral\Contracts\Translate\TranslateChildInterface;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Austral\HttpBundle\EntityManager\DomainEntityManager;
use Austral\HttpBundle\Mapping\DomainFilterMapping;
use Austral\ToolsBundle\AustralTools;
use Austral\ToolsBundle\Services\Debug;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RequestContext;

/**
 * Austral Domain Service.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class DomainsManagement
{

  const string DOMAIN_ID_FOR_ALL_DOMAINS = "for-all-domains";
  const string DOMAIN_ID_MASTER = FilterByDomainInterface::DOMAIN_ID_MASTER;
  const string DOMAIN_ID_CURRENT = "current";

  /**
   * @var array<string, DomainInterface<T>>
   */
  protected array $domains = array();

  /**
   * @var array<string, DomainInterface<T>>
   */
  protected array $domainsWithoutVirtual = array();

  /**
   * @var array<string, DomainInterface<T>>
   */
  protected array $domainsMaster = array();

  /**
   * @var array<string, DomainInterface<T>>
   */
  protected array $domainsById = array();

  /**
   * @var array<string, string>
   */
  protected array $domainsIdByKeyname = array();

  /**
   * @var array<string, RequestContext>
   */
  protected array $requestContextByDomainId = array();

  /**
   * @var DomainInterface<T>|null
   */
  protected ?DomainInterface $currentDomain = null;

  /**
   * @var DomainInterface<T>|null
   */
  protected ?DomainInterface $currentDomainWithVirtual = null;

  /**
   * @var DomainInterface<T>|null
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
   * @param Mapping<T> $mapping
   * @param DomainEntityManager<T> $domainEntityManager
   * @param HttpRequest<T> $httpRequest
   * @param Debug $debug
   */
  public function __construct(
    #[Autowire(service: "austral.entity.mapping")] protected Mapping $mapping,
    #[Autowire(service: "austral.entity_manager.domain")] protected DomainEntityManager $domainEntityManager,
    #[Autowire(service: "austral.http.request")] protected HttpRequest $httpRequest,
    #[Autowire(service: "austral.tools.debug")] protected Debug $debug)
  {
    $this->createVirtualDomain(self::DOMAIN_ID_FOR_ALL_DOMAINS);
  }

  /**
   * @return string|null
   */
  public function getHost(): ?string
  {
    return $this->httpRequest->getRequest()?->getHost();
  }

  /**
   * getHttpRequest
   * @return HttpRequest<T>
   */
  public function getHttpRequest(): HttpRequest
  {
    return $this->httpRequest;
  }

  /**
   * @param bool $detectAutoHost
   * @return $this
   * @throws QueryException
   */
  public function initialize(bool $detectAutoHost = true): self
  {
    $this->debug->stopWatchStart("austral.domain_management.initialize", "austral.http.domain_management");
    if(!$this->domains)
    {
      $this->domains = $this->domainEntityManager->selectAllEnabledDomains();

      /** @var DomainInterface<T> $domain */
      foreach($this->domains as $domain)
      {
        $domain->setRequestLanguage($this->httpRequest->getLanguage());
        $this->domainsById[$domain->getId()] = $domain;
        if($this->getHost() === $domain->getDomain() && $detectAutoHost)
        {
          $this->currentDomainWithVirtual = $domain;
          if($domain->getIsVirtual() && $domain->getMaster())
          {
            $this->currentDomain = $domain->getMaster();
          }
          else
          {
            $this->currentDomain = $domain;
          }
        }
        if(!$this->domainMaster && $domain->getIsMaster())
        {
          $this->domainMaster = $domain;
          $this->domainsIdByKeyname[self::DOMAIN_ID_MASTER] = $domain->getId();
          $this->setFilterDomainId($domain->getId());
        }
        if(!$domain->getIsRedirect())
        {
          if(!$domain->getIsVirtual())
          {
            $this->domainsWithoutVirtual[$domain->getId()] = $domain;
            if($domain->getIsMaster())
            {
              $this->domainsMaster[$domain->getId()] = $domain;
            }
          }
          $this->domainsIdByKeyname[$domain->getKeyname()] = $domain->getId();
          $requestContext = new RequestContext();
          if($this->httpRequest->getRequest())
          {
            $requestContext->fromRequest($this->httpRequest->getRequest());
          }
          $requestContext->setScheme($domain->getScheme());
          $this->requestContextByDomainId[$domain->getId()] = $requestContext->setHost($domain->getDomain());
        }
      }

      if($this->getDomainForAll())
      {
        $this->domainsById[$this->getDomainForAll()->getId()] = $this->getDomainForAll();
        if(!$this->domainMaster)
        {
          $domainMaster = $this->createVirtualDomain(self::DOMAIN_ID_MASTER);
          $domainMaster->setIsMaster(true);
          $this->domainMaster = $domainMaster;
          $this->currentDomain = $domainMaster;
          $this->setFilterDomainId($domainMaster->getId());
          $this->domainsWithoutVirtual[$domainMaster->getId()] = $domainMaster;
        }
      }
    }
    $this->debug->stopWatchStop("austral.domain_management.initialize");
    return $this;
  }

  /**
   * @param string $keyname
   *
   * @return DomainInterface<T>
   */
  protected function createVirtualDomain(string $keyname): DomainInterface
  {
    $domainClass = $this->domainEntityManager->getClass();
    $domain = new $domainClass();
    $domain->setId($keyname);
    $domain->setName($keyname);
    $domain->setKeyname($keyname);
    $domain->setDomain($this->getHost() ?? "");
    $domain->setIsVirtual(true);
    $domain->setRequestLanguage($this->httpRequest->getLanguage());
    $this->domainsById[$keyname] = $domain;
    $this->domainsIdByKeyname[$domain->getKeyname()] = $domain->getId();
    return $domain;
  }

  /**
   * @return bool
   */
  public function getEnabledDomainWithoutVirtual(): bool
  {
    return count($this->domainsMaster) > 1;
  }

  /**
   * @return array<string, DomainInterface<T>>
   */
  public function getDomainsWithoutVirtual(): array
  {
    return $this->domainsWithoutVirtual;
  }

  /**
   * @return array<string, DomainInterface<T>>
   */
  public function getDomains(): array
  {
    return $this->domains;
  }

  /**
   * @return DomainInterface<T>|null
   */
  public function getDomainMaster(): ?DomainInterface
  {
    return $this->domainMaster;
  }

  /**
   * @param bool $withoutVirtual
   *
   * @return DomainInterface<T>|null
   */
  public function getCurrentDomain(bool $withoutVirtual = true): ?DomainInterface
  {
    return $withoutVirtual ? $this->currentDomain : $this->currentDomainWithVirtual;
  }

  /**
   * @param DomainInterface<T> $currentDomain
   *
   * @return $this
   */
  public function setCurrentDomain(DomainInterface $currentDomain): self
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
   * @param string|null $domainId
   * @param bool $withoutVirtual
   *
   * @return string|null
   */
  public function getReelDomainId(?string $domainId = DomainsManagement::DOMAIN_ID_MASTER, bool $withoutVirtual = true): ?string
  {
    $domainId = $domainId === "current" ? $this->getCurrentDomain($withoutVirtual)?->getId() : null;
    $domainId = $domainId ?? DomainsManagement::DOMAIN_ID_MASTER;
    return array_key_exists($domainId, $this->domainsIdByKeyname) ? $this->domainsIdByKeyname[$domainId] : $domainId;
  }

  /**
   * @param string|null $id
   *
   * @return ?DomainInterface<T>
   */
  public function getDomainById(?string $id = null): ?DomainInterface
  {
    $id = $id && array_key_exists($id, $this->domainsIdByKeyname) ? $this->domainsIdByKeyname[$id] : $id;
    if($id)
    {
      return array_key_exists($id, $this->domainsById) ? $this->domainsById[$id] : null;
    }
    return $this->currentDomain;
  }

  /**
   * @param string|null $keyname
   *
   * @return ?DomainInterface<T>
   */
  public function getDomainByKeyname(?string $keyname = null): ?DomainInterface
  {
    return $this->getDomainById($this->getDomainIdByKeyname($keyname));
  }

  /**
   * @param string|null $keyname
   *
   * @return ?string
   */
  public function getDomainIdByKeyname(?string $keyname = null): ?string
  {
    return $keyname && array_key_exists($keyname, $this->domainsIdByKeyname) ? $this->domainsIdByKeyname[$keyname] : null;
  }

  /**
   * @return DomainInterface<T>|null
   */
  public function getDomainForAll(): ?DomainInterface
  {
    return $this->getDomainById(self::DOMAIN_ID_FOR_ALL_DOMAINS);
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
   * @return $this
   */
  public function setFilterDomainId(?string $filterDomainId): self
  {
    $this->filterDomainId = $filterDomainId;
    return $this;
  }

  /**
   * @return ?DomainInterface<T>
   */
  public function getFilterDomain(): ?DomainInterface
  {
    return $this->getDomainById($this->filterDomainId);
  }

  /**
   * @var array<int, FilterByDomainInterface<T>>
   */
  protected array $objectsDomainAttachement = array();

  /**
   * @return array<int, FilterByDomainInterface<T>>
   */
  public function getObjectsDomainAttachement(): array
  {
    return $this->objectsDomainAttachement;
  }

  /**
   * @param string $domainId
   * @param bool $withoutVirtual
   *
   * @return RequestContext|null
   */
  public function getRequestContextByDomainId(string $domainId, bool $withoutVirtual = true): ?RequestContext
  {
    return AustralTools::valueByKey($this->requestContextByDomainId, $this->getReelDomainId($domainId, $withoutVirtual) ?? "");
  }

  /**
   * @param EntityInterface<T> $object
   * @param string|null $domainId
   *
   * @return boolean
   */
  public function objectAttachementByDomainId(EntityInterface $object, ?string $domainId = null): bool
  {
    if($object instanceof FilterByDomainInterface)
    {
      /** @var DomainFilterMapping<T>|null $domainFilterMapping */
      $domainFilterMapping = $this->mapping->getEntityClassMapping($object->getClassnameForMapping(), DomainFilterMapping::class);
      if($domainFilterMapping)
      {
        if($domainFilterMapping->getAutoDomainId())
        {
          return $object->getDomainId() === $this->getReelDomainId($domainId);
        }
      }
    }
    return false;
  }

  /**
   * @param EntityInterface<T> $object
   * @param bool $withChild
   *
   * @return $this
   */
  public function objectDomainAttachement(EntityInterface $object, bool $withChild = true): self
  {
    if($object instanceof TranslateChildInterface)
    {
      $object = $object->getMaster();
    }

    if($object instanceof FilterByDomainInterface)
    {
      /** @var DomainFilterMapping<T>|null $domainFilterMapping */
      $domainFilterMapping = $this->mapping->getEntityClassMapping($object->getClassnameForMapping(), DomainFilterMapping::class);
      if($domainFilterMapping)
      {
        if($domainFilterMapping->getAutoDomainId() && $domainFilterMapping->getAutoAttachement() && $this->getFilterDomainId())
        {
          if(!$object->getDomainId())
          {
            $object->setDomainId($this->getFilterDomainId());
          }
        }
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
    }
    return $this;
  }

}