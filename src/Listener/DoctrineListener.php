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

use Austral\Contracts\Entity\EntityInterface;
use Austral\HttpBundle\Services\DomainsManagement;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Austral Doctrine Listener.
 * @template TObjectManager of ObjectManager
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineListener
{

  /**
   * @var bool
   */
  protected bool $postFlush = false;


  /**
   * DoctrineListener constructor
   *
   * @param DomainsManagement<T> $domainAttachement
   */
  public function __construct(
    #[Autowire(service: "austral.http.domains.management")] protected DomainsManagement $domainAttachement)
  {
  }

  /**
   * @return string[]
   */
  public function getSubscribedEvents(): array
  {
    return array(
      Events::postLoad,
      Events::prePersist,
      Events::preUpdate,
      Events::postRemove,
      Events::postFlush,
    );
  }

  /**
   * @param LifecycleEventArgs<TObjectManager> $args
   */
  public function postLoad(LifecycleEventArgs $args): void
  {
  }

  /**
   * @param LifecycleEventArgs<TObjectManager> $args
   */
  public function prePersist(LifecycleEventArgs $args): void
  {
    $object = $args->getObject();
    if($object instanceof EntityInterface)
    {
      $this->domainAttachement->objectDomainAttachement($object);
    }
  }

  /**
   * @param LifecycleEventArgs<TObjectManager> $args
   */
  public function preUpdate(LifecycleEventArgs $args): void
  {
    if(!$this->postFlush)
    {
      $object = $args->getObject();
      if($object instanceof EntityInterface)
      {
        $this->domainAttachement->objectDomainAttachement($object);
      }
    }
  }

  /**
   * @param LifecycleEventArgs<TObjectManager> $args
   *
   */
  public function postRemove(LifecycleEventArgs $args): void
  {
  }

  /**
   * @param ObjectManager $args
   *
   */
  public function postFlush(ObjectManager $args): void
  {
    if(!$this->postFlush)
    {
      $this->postFlush = true;
      foreach($this->domainAttachement->getObjectsDomainAttachement() as $object) {
        $args->persist($object);
      }
      $args->flush();
    }
  }

}