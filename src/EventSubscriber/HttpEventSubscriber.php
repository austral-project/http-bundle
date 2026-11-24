<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\EventSubscriber;

use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;
use Austral\HttpBundle\EventSubscriber\Interfaces\HttpEventSubscriberInterface;
use Austral\ToolsBundle\Configuration\ConfigurationInterface;
use Austral\ToolsBundle\Services\Debug;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral Http EventSubscriber.
 *
 * @template T of object
 *
 * @implements HttpEventSubscriberInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class HttpEventSubscriber implements EventSubscriberInterface, HttpEventSubscriberInterface
{
  /**
   * HttpSubscriber constructor.
   *
   * @param ConfigurationInterface $configuration
   * @param Debug                  $debug
   */
  public function __construct(
    protected ConfigurationInterface $configuration,
    protected Debug $debug
  ) {
  }

  /**
   * {@inheritDoc}
   */
  abstract public static function getSubscribedEvents(): array;

  /**
   * {@inheritDoc}
   */
  abstract public function onRequestInitialise(HttpEventInterface $httpEvent): void;

  /**
   * {@inheritDoc}
   */
  abstract public function onRequest(HttpEventInterface $httpEvent): void;

  /**
   * {@inheritDoc}
   */
  abstract public function onController(HttpEventInterface $httpEvent): void;

  /**
   * {@inheritDoc}
   */
  abstract public function onResponse(HttpEventInterface $httpEvent): void;

  /**
   * {@inheritDoc}
   */
  abstract public function onException(HttpEventInterface $httpEvent): void;
}
