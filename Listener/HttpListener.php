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

use Austral\HttpBundle\Configuration\HttpConfiguration;
use Austral\HttpBundle\Controller\Interfaces\HttpControllerInterface;
use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;

use Austral\AdminBundle\Event\AdminHttpEvent;

use Austral\ToolsBundle\AustralTools;
use Austral\ToolsBundle\Services\Debug;
use Austral\WebsiteBundle\Entity\Interfaces\DomainInterface;
use Austral\WebsiteBundle\Event\WebsiteHttpEvent;
use Austral\WebsiteBundle\Services\Domain;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Austral Http Listener.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
class HttpListener
{
  /**
   * @var ContainerInterface
   */
  protected ContainerInterface $container;

  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * @var Debug
   */
  protected Debug $debug;

  /**
   * @var HttpConfiguration
   */
  protected HttpConfiguration $configuration;

  /**
   * @var HttpEventInterface|null
   */
  protected ?HttpEventInterface $httpEvent = null;

  /**
   * @var bool
   */
  protected bool $compressionGzipEnabled = false;

  /**
   * ControllerListener constructor.
   *
   * @param ContainerInterface $container
   * @param EventDispatcherInterface $eventDispatcher
   * @param HttpConfiguration $configuration
   * @param Debug $debug
   */
  public function __construct(ContainerInterface $container, EventDispatcherInterface $eventDispatcher, HttpConfiguration $configuration, Debug $debug)
  {
    $this->container = $container;
    $this->debug = $debug;
    $this->eventDispatcher = $eventDispatcher;
    $this->configuration = $configuration;
  }

  /**
   * @param RequestEvent $event
   *
   * @throws NonUniqueResultException
   */
  public function initRequest(RequestEvent $event)
  {
    $this->debug->stopWatchStart("init-request", "austral.http.listener");

    /** @var AttributeBagInterface $requestAttributes */
    $requestAttributes = $event->getRequest()->attributes;


    $domainService = $currentDomain = null;
    if($websiteDomainIsDefined = $this->container->has('austral.website.domain'))
    {
      /** @var Domain $domainService */
      $domainService = $this->container->get('austral.website.domain');

      /** @var DomainInterface $currentDomain */
      $currentDomain = $domainService->getCurrentDomain();
    }

    $currentLocal = null;
    if($requestAttributes->get('_austral_admin', false))
    {
      $this->compressionGzipEnabled = $this->configuration->get('compression_gzip.admin');
      $this->httpEvent = new AdminHttpEvent();

      if($websiteDomainIsDefined) {
        /** @var DomainInterface $masterDomain */
        $masterDomain = $domainService->getDomainMaster();
        if($currentDomain && $masterDomain && $masterDomain->getId() !== $currentDomain->getId()) {
          $urlRedirect = $event->getRequest()->getScheme() . "://" . $masterDomain->getDomain() . $event->getRequest()->getRequestUri();
          $response = new RedirectResponse($urlRedirect, 302);
          $event->setResponse($response);
        }
      }
      $currentLocal = $event->getRequest()->getSession()->get("austral_language_interface");
      if(!$event->getRequest()->attributes->has("language"))
      {
        $event->getRequest()->attributes->set("language", $this->container->getParameter('locale'));
      }
    }
    else if($requestAttributes->get('_austral_website', false)) {
      $this->compressionGzipEnabled = $this->configuration->get('compression_gzip.website');
      $this->httpEvent = new WebsiteHttpEvent();
      if($currentDomain && $currentDomain->getLanguage()) {
        $currentLocal = $currentDomain->getLanguage();
      }
      if($event->getRequest()->attributes->has("_locale"))
      {
        $currentLocal = $event->getRequest()->attributes->get("_locale");
      }
      if(!$event->getRequest()->attributes->has("language"))
      {
        $event->getRequest()->attributes->set("language", $currentLocal ? : $this->container->getParameter('locale'));
      }

    }
    else {
      $this->compressionGzipEnabled = $this->configuration->get('compression_gzip.other');
    }
    if($currentLocal)
    {
      $event->getRequest()->setLocale($currentLocal);
      $event->getRequest()->setDefaultLocale($currentLocal);
    }

    $this->debug->stopWatchStop("init-request");
  }

  /**
   * @param RequestEvent $event
   */
  public function onRequest(RequestEvent $event)
  {
    $this->debug->stopWatchStart("request", "austral.http.listener");
    if($this->httpEvent && $event->isMainRequest()) {
      $this->httpEvent->setKernelEvent($event);
      $this->eventDispatcher->dispatch($this->httpEvent, $this->httpEvent::EVENT_AUSTRAL_HTTP_REQUEST);
    }
    $this->debug->stopWatchStop("request");
  }

  /**
   * @param ControllerEvent $controllerEvent
   */
  public function onController(ControllerEvent $controllerEvent)
  {
    $this->debug->stopWatchStart("controller", "austral.http.listener");
    if($this->httpEvent && $controllerEvent->isMainRequest()) {
      $eventController = $controllerEvent->getController();
      if(!is_array($eventController)) {
        return;
      }
      $controller = $this->isHttpController($eventController[0]);
      if($controller && $this->httpEvent) {
        $this->httpEvent->setKernelEvent($controllerEvent);
        if($this->httpEvent->getHandler()) {
          $this->eventDispatcher->dispatch($this->httpEvent, $this->httpEvent::EVENT_AUSTRAL_HTTP_CONTROLLER);
          $controller->setHandlerManager($this->httpEvent->getHandler());
        }
      }
    }
    $this->debug->stopWatchStop("controller");
  }

  /**
   * @param ResponseEvent $responseEvent
   */
  public function onResponse(ResponseEvent $responseEvent)
  {
    $this->debug->stopWatchStart("response", "austral.http.listener");
    if($this->httpEvent && $responseEvent->isMainRequest()) {
      $this->httpEvent->setKernelEvent($responseEvent);

      if($this->httpEvent->getHandler()) {
        $this->eventDispatcher->dispatch($this->httpEvent, $this->httpEvent::EVENT_AUSTRAL_HTTP_RESPONSE);
      }
      $response = $responseEvent->getResponse();
      if($this->compressionGzipEnabled) {
        $encodings = $responseEvent->getRequest()->getEncodings();
        if(in_array('gzip', $encodings) && function_exists('gzencode')) {
          $content = gzencode($response->getContent());
          $response->setContent($content);
          $response->headers->set('Content-encoding', 'gzip');
        }
        elseif(in_array('deflate', $encodings) && function_exists('gzdeflate')) {
          $content = gzdeflate($response->getContent());
          $response->setContent($content);
          $response->headers->set('Content-encoding', 'deflate');
        }
      }
    }
    $this->debug->stopWatchStart("response");
  }

  /**
   * @param FinishRequestEvent $finishRequestEvent
   */
  public function onFinishRequest(FinishRequestEvent $finishRequestEvent)
  {
  }

  /**
   * @param $controller
   *
   * @return HttpControllerInterface
   */
  protected function isHttpController($controller): ?HttpControllerInterface
  {
    return AustralTools::usedImplements(get_class($controller),HttpControllerInterface::class) ? $controller : null;
  }

}
