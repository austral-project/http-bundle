<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\EventSubscriber\Interfaces;

use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;

/**
 * Austral Http EventSubscriber Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpEventSubscriberInterface
{

  /**
   * @return array
   */
  public static function getSubscribedEvents(): array;

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return mixed
   */
  public function onRequest(HttpEventInterface $httpEvent);

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return mixed
   */
  public function onController(HttpEventInterface $httpEvent);

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return mixed
   */
  public function onResponse(HttpEventInterface $httpEvent);


}