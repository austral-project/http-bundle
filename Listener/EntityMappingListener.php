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

use Austral\HttpBundle\Annotation\DomainFilter;

use Austral\EntityBundle\EntityAnnotation\EntityAnnotations;
use Austral\EntityBundle\Event\EntityMappingEvent;
use Austral\EntityBundle\Mapping\EntityMapping;

use Austral\HttpBundle\Mapping\DomainFilterMapping;

/**
 * Austral EntityMapping Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class EntityMappingListener
{

  public function __construct()
  {
  }

  /**
   * @param EntityMappingEvent $entityAnnotationEvent
   *
   * @return void
   * @throws \Exception
   */
  public function mapping(EntityMappingEvent $entityAnnotationEvent)
  {
    $initialiseEntitesAnnotations = $entityAnnotationEvent->getEntitiesAnnotations();
    /**
     * @var EntityAnnotations $entityAnnotation
     */
    foreach($initialiseEntitesAnnotations->all() as $entityAnnotation)
    {
      if(array_key_exists(DomainFilter::class, $entityAnnotation->getClassAnnotations()))
      {
        /** @var DomainFilter $domainFilterAnnotation */
        $domainFilterAnnotation = $entityAnnotation->getClassAnnotations()[DomainFilter::class];
        if(!$entityMapping = $entityAnnotationEvent->getMapping()->getEntityMapping($entityAnnotation->getClassname()))
        {
          $entityMapping = new EntityMapping($entityAnnotation->getClassname(), $entityAnnotation->getSlugger());
        }
        $domainFilterMapping = new DomainFilterMapping();
        $domainFilterMapping->setAutoDomainId($domainFilterAnnotation->autoDomainId);
        $domainFilterMapping->setForAllDomainEnabled($domainFilterAnnotation->forAllDomainEnabled);
        $domainFilterMapping->setAutoAttachement($domainFilterAnnotation->autoAttachement);

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
        $entityAnnotationEvent->getMapping()->addEntityMapping($entityAnnotation->getClassname(), $entityMapping);
      }
    }
  }

}
