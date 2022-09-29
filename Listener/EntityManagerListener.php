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
use Austral\HttpBundle\Mapping\DomainFilterMapping;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\HttpBundle\Services\HttpRequest;

/**
 * Austral EntityManager Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class EntityManagerListener
{

  /**
   * @var DomainsManagement
   */
  protected DomainsManagement $domainsManagement;

  /**
   * @var Mapping
   */
  protected Mapping $mapping;

  /**
   * @var HttpRequest
   */
  protected HttpRequest $httpRequest;

  /**
   * @param Mapping $mapping
   * @param DomainsManagement $domainsManagement
   * @param HttpRequest $httpRequest
   */
  public function __construct(Mapping $mapping, DomainsManagement $domainsManagement, HttpRequest $httpRequest)
  {
    $this->mapping = $mapping;
    $this->domainsManagement = $domainsManagement;
    $this->httpRequest = $httpRequest;
  }

  /**
   * @param EntityManagerEvent $entityManagerEvent
   *
   * @throws \Exception
   */
  public function duplicateUrlParameter(EntityManagerEvent $entityManagerEvent)
  {
    $routeParams = $this->httpRequest->getRequest()->attributes->get("_route_params");
    /** @var DomainFilterMapping $domainFilterMapping */
    if(array_key_exists("domainId", $routeParams) && ($domainFilterMapping = $this->mapping->getEntityClassMapping($entityManagerEvent->getSourceObject()->getClassnameForMapping(), DomainFilterMapping::class)))
    {
      if($domainFilterMapping->getAutoDomainId())
      {
        $entityManagerEvent->getObject()->setDomainId($routeParams["domainId"]);
      }
    }
  }

}