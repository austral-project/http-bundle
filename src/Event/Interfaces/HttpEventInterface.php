<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Event\Interfaces;

use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Services\HttpRequest;
use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * Austral Http Event Interface.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpEventInterface
{

  const string EVENT_AUSTRAL_HTTP_REQUEST_INITIALISE = "austral.event.http.request.initialise";
  const string EVENT_AUSTRAL_HTTP_REQUEST = "austral.event.http.request";
  const string EVENT_AUSTRAL_HTTP_CONTROLLER = "austral.event.http.controller";
  const string EVENT_AUSTRAL_HTTP_RESPONSE = "austral.event.http.response";
  const string EVENT_AUSTRAL_HTTP_EXCEPTION = "austral.event.http.exception";
  const string EVENT_AUSTRAL_HTTP_REQUEST_FINISH = "austral.event.http.request.finish";

  /**
   * FormEvent constructor.
   *
   */
  public function __construct();

  /**
   * @return HttpHandlerInterface<T>|null
   */
  public function getHandler(): ?HttpHandlerInterface;

  /**
   * @param HttpHandlerInterface<T> $handler
   *
   * @return $this
   */
  public function setHandler(HttpHandlerInterface $handler): HttpEventInterface;

  /**
   * @return HttpRequest<T>|null
   */
  public function getHttpRequest(): ?HttpRequest;

  /**
   * @param HttpRequest<T> $httpRequest
   *
   * @return $this
   */
  public function setHttpRequest(HttpRequest $httpRequest): HttpEventInterface;

  /**
   * @return KernelEvent|null
   */
  public function getKernelEvent(): ?KernelEvent;

  /**
   * @param KernelEvent $kernelEvent
   *
   * @return $this
   */
  public function setKernelEvent(KernelEvent $kernelEvent): HttpEventInterface;

}