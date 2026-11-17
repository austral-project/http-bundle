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
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\ToolsBundle\Configuration\ConfigurationInterface;
use Austral\ToolsBundle\Services\Debug;
use Doctrine\ORM\Query\QueryException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment;

/**
 * Austral Http EventSubscriber.
 * @template T of object
 * @implements HttpEventSubscriberInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
abstract class HttpEventSubscriber implements EventSubscriberInterface, HttpEventSubscriberInterface
{

  /**
   * HttpSubscriber constructor.
   *
   * @param ContainerInterface $container
   * @param ConfigurationInterface $configuration
   * @param DomainsManagement<T> $domainsManagement
   * @param Debug $debug
   *
   * @throws QueryException
   */
  public function __construct(
    #[Autowire(service: "service_container")] protected ContainerInterface $container,
    #[Autowire(service: "austral.http.config")] protected ConfigurationInterface $configuration,
    #[Autowire(service: "austral.http.domains.management")] protected DomainsManagement $domainsManagement,
    #[Autowire(service: "austral.tools.debug")] protected Debug $debug)
  {
    $this->domainsManagement->initialize();
  }

  /**
   * @inheritDoc
   */
  abstract public static function getSubscribedEvents(): array;

  /**
   * @inheritDoc
   */
  abstract public function onRequestInitialise(HttpEventInterface $httpEvent): void;

  /**
   * @inheritDoc
   */
  abstract public function onRequest(HttpEventInterface $httpEvent): void;

  /**
   * @inheritDoc
   */
  abstract public function onController(HttpEventInterface $httpEvent): void;

  /**
   * @inheritDoc
   */
  abstract public function onResponse(HttpEventInterface $httpEvent): void;

  /**
   * @inheritDoc
   */
  abstract public function onException(HttpEventInterface $httpEvent): void;


}