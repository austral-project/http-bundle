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
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpEventSubscriberInterface
{

  /**
   * @return array<string, string>
   */
  public static function getSubscribedEvents(): array;


  /**
   * @param HttpEventInterface<T> $httpEvent
   * @return void
   */
  public function onRequestInitialise(HttpEventInterface $httpEvent): void;

  /**
   * @param HttpEventInterface<T> $httpEvent
   * @return void
   */
  public function onRequest(HttpEventInterface $httpEvent): void;

  /**
   * @param HttpEventInterface<T> $httpEvent
   * @return void
   */
  public function onController(HttpEventInterface $httpEvent): void;

  /**
   * @param HttpEventInterface<T> $httpEvent
   * @return void
   */
  public function onResponse(HttpEventInterface $httpEvent): void;

  /**
   * @param HttpEventInterface<T> $httpEvent
   * @return void
   */
  public function onException(HttpEventInterface $httpEvent): void;


}