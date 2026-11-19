<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Listener;

use Austral\EntityBundle\Event\EntityManagerEvent;
use Austral\EntityBundle\Mapping\Mapping;
use Austral\Contracts\Domain\FilterByDomainInterface;
use Austral\HttpBundle\Mapping\DomainFilterMapping;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\HttpBundle\Services\HttpRequest;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Austral EntityManager Listener.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerListener
{

  /**
   * @param Mapping<T> $mapping
   * @param DomainsManagement<T> $domainsManagement
   * @param HttpRequest<T> $httpRequest
   */
  public function __construct(
    #[Autowire(service: "austral.entity.mapping")] protected Mapping $mapping,
    #[Autowire(service: "austral.http.domains.management")] protected DomainsManagement $domainsManagement,
    #[Autowire(service: "austral.http.request")] protected HttpRequest $httpRequest)
  {
  }

  /**
   * @param EntityManagerEvent<T> $entityManagerEvent
   *
   * @throws \Exception
   */
  public function duplicate(EntityManagerEvent $entityManagerEvent): void
  {
    $routeParams = $this->httpRequest->getRequest()?->attributes->get("_route_params") ?: [];

    $sourceObjectClassForMapping = $entityManagerEvent->getSourceObject()?->getClassnameForMapping();
    $domainFilterMapping = $sourceObjectClassForMapping ? $this->mapping->getEntityClassMapping($sourceObjectClassForMapping, DomainFilterMapping::class) : null;

    /** @var FilterByDomainInterface<T> $object */
    $object = $entityManagerEvent->getObject();
    if(array_key_exists("domainId", $routeParams) && $domainFilterMapping instanceof DomainFilterMapping)
    {
      $domainByRouteParameter = $this->domainsManagement->getDomainById($routeParams["domainId"]);
      if($domainFilterMapping->getAutoDomainId() && $domainByRouteParameter->getIsMaster())
      {
        $object->setDomainId($routeParams["domainId"]);
      }
    }
  }

}