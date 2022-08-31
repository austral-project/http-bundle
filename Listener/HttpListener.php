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
use Austral\HttpBundle\Event\HttpEvent;
use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;

use Austral\AdminBundle\Event\AdminHttpEvent;

use Austral\HttpBundle\Services\HttpRequest;
use Austral\ToolsBundle\AustralTools;
use Austral\ToolsBundle\Services\Debug;
use Austral\WebsiteBundle\Event\WebsiteHttpEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
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
   * @var HttpRequest|null
   */
  protected ?HttpRequest $httpRequest = null;

  /**
   * @var HttpEventInterface|HttpEvent|null
   */
  protected ?HttpEventInterface $httpEvent = null;

  /**
   * ControllerListener constructor.
   *
   * @param ContainerInterface $container
   * @param EventDispatcherInterface $eventDispatcher
   * @param HttpConfiguration $configuration
   * @param HttpRequest $httpRequest
   * @param Debug $debug
   */
  public function __construct(ContainerInterface $container,
    EventDispatcherInterface $eventDispatcher,
    HttpConfiguration $configuration,
    HttpRequest $httpRequest,
    Debug $debug)
  {
    $this->container = $container;
    $this->debug = $debug;
    $this->eventDispatcher = $eventDispatcher;
    $this->configuration = $configuration;
    $this->httpRequest = $httpRequest;
  }

  /**
   * @param RequestEvent $event
   */
  public function initRequest(RequestEvent $event)
  {
    $this->debug->stopWatchStart("init-request", "austral.http.listener");

    /** @var AttributeBagInterface $requestAttributes */
    $requestAttributes = $event->getRequest()->attributes;

    $this->httpRequest->setRequest($event->getRequest());
    if($requestAttributes->get('_austral_admin', false))
    {
      $this->httpRequest->setCompressionGzipEnabled($this->configuration->get('compression_gzip.admin'));
      $this->httpEvent = new AdminHttpEvent();
    }
    else if($requestAttributes->get('_austral_website', false))
    {
      $this->httpRequest->setCompressionGzipEnabled($this->configuration->get('compression_gzip.website'));
      $this->httpEvent = new WebsiteHttpEvent();
    }
    else if($httpEventName = $requestAttributes->get('_austral_http_event'))
    {
      $this->httpRequest->setCompressionGzipEnabled($this->configuration->get('compression_gzip.other'));
      if(class_exists($httpEventName))
      {
        $this->httpEvent = new $httpEventName();
      }
    }

    if($this->httpEvent)
    {
      $this->httpEvent->setKernelEvent($event);
      $this->httpEvent->setHttpRequest($this->httpRequest);
      $this->eventDispatcher->dispatch($this->httpEvent, $this->httpEvent::EVENT_AUSTRAL_HTTP_REQUEST_INITIALISE);
    }

    $event->getRequest()->setLocale($this->httpRequest->getLanguage());
    $event->getRequest()->setDefaultLocale($this->httpRequest->getLanguage());
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
   * @param ExceptionEvent $event
   */
  public function onException(ExceptionEvent $event)
  {
    $this->debug->stopWatchStart("exception", "austral.http.listener");
    if($this->httpEvent && $event->isMainRequest()) {
      $this->httpEvent->setKernelEvent($event);
      $this->eventDispatcher->dispatch($this->httpEvent, $this->httpEvent::EVENT_AUSTRAL_HTTP_EXCEPTION);
    }
    $this->debug->stopWatchStop("exception");
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
      if($this->httpRequest->getIsCompressionGzipEnabled()) {
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
    $this->debug->stopWatchStart("finish-request", "austral.http.listener");
    if($this->httpEvent && $finishRequestEvent->isMainRequest()) {
      $this->httpEvent->setKernelEvent($finishRequestEvent);
      $this->eventDispatcher->dispatch($this->httpEvent, $this->httpEvent::EVENT_AUSTRAL_HTTP_REQUEST_FINISH);
    }
    $this->debug->stopWatchStart("finish-request");
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
