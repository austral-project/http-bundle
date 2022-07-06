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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Austral Http EventSubscriber.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
abstract class HttpEventSubscriber implements EventSubscriberInterface, HttpEventSubscriberInterface
{

  /**
   * @var ContainerInterface
   */
  protected ContainerInterface $container;

  /**
   * @var ConfigurationInterface
   */
  protected ConfigurationInterface $configuration;

  /**
   * @var Debug
   */
  protected Debug $debug;

  /**
   * HttpSubscriber constructor.
   *
   * @param ContainerInterface $container
   * @param ConfigurationInterface $configuration
   * @param Debug $debug
   */
  public function __construct(ContainerInterface $container, ConfigurationInterface $configuration, Debug $debug)
  {
    $this->container = $container;
    $this->configuration = $configuration;
    $this->debug = $debug;
  }

  /**
   * @return array
   */
  abstract public static function getSubscribedEvents(): array;


  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return mixed
   */
  abstract public function onRequest(HttpEventInterface $httpEvent);

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return mixed
   */
  abstract public function onController(HttpEventInterface $httpEvent);

  /**
   * @param HttpEventInterface $httpEvent
   *
   * @return mixed
   */
  abstract public function onResponse(HttpEventInterface $httpEvent);


}