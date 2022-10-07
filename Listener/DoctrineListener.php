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

use Austral\EntityBundle\Entity\EntityInterface;
use Austral\HttpBundle\Services\DomainsManagement;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManager;

/**
 * Austral Doctrine Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
class DoctrineListener implements EventSubscriber
{

  /**
   * @var mixed
   */
  protected $name;

  /**
   * @var EntityManager
   */
  protected EntityManager $entityManager;

  /**
   * @var bool
   */
  protected bool $postFlush = false;

  /**
   * @var DomainsManagement
   */
  protected DomainsManagement $domainAttachement;

  /**
   * DoctrineListener constructor.
   */
  public function __construct(DomainsManagement $domainAttachement)
  {
    $parts = explode('\\', $this->getNamespace());
    $this->name = end($parts);
    $this->domainAttachement = $domainAttachement;
  }

  /**
   * @return array
   */
  public function getSubscribedEvents()
  {
    return array(
      'postLoad',
      'prePersist',
      'preUpdate',
      'postRemove',
      "postFlush"
    );
  }

  /**
   * @param EventArgs $args
   */
  public function postLoad(EventArgs $args)
  {
  }

  /**
   * @param LifecycleEventArgs $args
   */
  public function prePersist(LifecycleEventArgs $args)
  {
    if(($object = $args->getObject()) && $object instanceof EntityInterface)
    {
      $this->domainAttachement->objectDomainAttachement($object, true);
    }
  }

  /**
   * @param LifecycleEventArgs $args
   */
  public function preUpdate(LifecycleEventArgs $args)
  {
    if(!$this->postFlush)
    {
      if(($object = $args->getObject()) && $object instanceof EntityInterface)
      {
        $this->domainAttachement->objectDomainAttachement($object, true);
      }
    }
  }

  /**
   * @param LifecycleEventArgs $args
   *
   */
  public function postRemove(LifecycleEventArgs $args)
  {
  }

  /**
   * @param PostFlushEventArgs $args
   *
   */
  public function postFlush(PostFlushEventArgs $args)
  {
    if(!$this->postFlush)
    {
      $this->postFlush = true;
      $entityManager = $args->getObjectManager();
      foreach($this->domainAttachement->getObjectsDomainAttachement() as $object) {
        $entityManager->persist($object);
      }
      $entityManager->flush();
    }
  }

  /**
   * Get an event adapter to handle event specific
   * methods
   *
   * @param EventArgs $args
   *
   * @return EventArgs
   */
  protected function getEventAdapter(EventArgs $args): EventArgs
  {
    return $args;
  }

  /**
   * @return string
   */
  protected function getNamespace(): string
  {
    return __NAMESPACE__;
  }

}