<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Event;

use Austral\HttpBundle\Event\Interfaces\HttpEventInterface;
use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Services\HttpRequest;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Http Event.
 *
 * @template T of object
 *
 * @implements HttpEventInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class HttpEvent extends Event implements HttpEventInterface
{
  /**
   * @var HttpHandlerInterface<T>|null
   */
  private ?HttpHandlerInterface $handler = null;

  /**
   * @var HttpRequest<T>|null
   */
  private ?HttpRequest $httpRequest = null;

  /**
   * @var KernelEvent|null
   */
  private ?KernelEvent $kernelEvent = null;

  /**
   * FormEvent constructor.
   */
  public function __construct()
  {
  }

  /**
   * @return HttpHandlerInterface<T>|null
   */
  public function getHandler(): ?HttpHandlerInterface
  {
    return $this->handler;
  }

  /**
   * @param HttpHandlerInterface<T> $handler
   *
   * @return $this
   */
  public function setHandler(HttpHandlerInterface $handler): self
  {
    $this->handler = $handler;

    return $this;
  }

  /**
   * @return HttpRequest<T>|null
   */
  public function getHttpRequest(): ?HttpRequest
  {
    return $this->httpRequest;
  }

  /**
   * @param HttpRequest<T> $httpRequest
   *
   * @return $this
   */
  public function setHttpRequest(HttpRequest $httpRequest): self
  {
    $this->httpRequest = $httpRequest;

    return $this;
  }

  /**
   * @return KernelEvent|null
   */
  public function getKernelEvent(): ?KernelEvent
  {
    return $this->kernelEvent;
  }

  /**
   * @param KernelEvent $kernelEvent
   *
   * @return $this
   */
  public function setKernelEvent(KernelEvent $kernelEvent): self
  {
    $this->kernelEvent = $kernelEvent;

    return $this;
  }
}
