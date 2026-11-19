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

use Austral\EntityBundle\EntityAttributes\EntityAttributes;
use Austral\HttpBundle\Attribute\DomainFilter;

use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\EntityMapping;

use Austral\HttpBundle\Mapping\DomainFilterMapping;

/**
 * Austral EntityMapping Listener.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityMappingListener
{

  public function __construct()
  {
  }

  /**
   * @param EntityMappingEvent<T> $entityAttributeEvent
   *
   * @return void
   * @throws \Exception
   */
  public function mapping(EntityMappingEvent $entityAttributeEvent): void
  {
    $initialiseEntitesAttributes = $entityAttributeEvent->getEntitiesAttributes();
    /**
     * @var EntityAttributes<T> $entityAttribute
     */
    foreach($initialiseEntitesAttributes->all() as $entityAttribute)
    {
      if(array_key_exists(DomainFilter::class, $entityAttribute->getClassAttributes()))
      {
        /** @var DomainFilter $domainFilterAttribute */
        $domainFilterAttribute = $entityAttribute->getClassAttributes()[DomainFilter::class];
        if(!$entityMapping = $entityAttributeEvent->getMapping()->getEntityMapping($entityAttribute->getClassname()))
        {
          $entityMapping = new EntityMapping($entityAttribute->getClassname(), $entityAttribute->getSlugger());
        }
        /** @var DomainFilterMapping<T> $domainFilterMapping */
        $domainFilterMapping = new DomainFilterMapping();
        $domainFilterMapping->setAutoDomainId($domainFilterAttribute->autoDomainId);
        $domainFilterMapping->setForAllDomainEnabled($domainFilterAttribute->forAllDomainEnabled);
        $domainFilterMapping->setAutoAttachement($domainFilterAttribute->autoAttachement);

        if($domainFilterMapping->getAutoDomainId())
        {
          if(!method_exists($entityMapping->entityClass, "getDomainId") || !method_exists($entityMapping->entityClass, "setDomainId"))
          {
            throw new \Exception("{$entityMapping->entityClass} has DomainFilter annotation but method getDomainId or setDomainId is not exist");
          }
        }
        elseif(!$domainFilterMapping->getForAllDomainEnabled())
        {
          if(!method_exists($entityMapping->entityClass, "getDomainIds") || !method_exists($entityMapping->entityClass, "setDomainIds"))
          {
            throw new \Exception("{$entityMapping->entityClass} has DomainFilter annotation but method getDomainIds or setDomainIds is not exist");
          }
        }
        $entityMapping->addEntityClassMapping($domainFilterMapping);
        $entityAttributeEvent->getMapping()->addEntityMapping($entityAttribute->getClassname(), $entityMapping);
      }
    }
  }

}
