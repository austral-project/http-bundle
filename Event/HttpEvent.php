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
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Austral Http Event.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
abstract class HttpEvent extends Event implements HttpEventInterface
{

  /**
   * @var HttpHandlerInterface|null
   */
  private ?HttpHandlerInterface $handler = null;

  /**
   * @var RequestEvent|ResponseEvent|null
   */
  private $kernelEvent;

  /**
   * FormEvent constructor.
   *
   */
  public function __construct()
  {
  }

  /**
   * @return HttpHandlerInterface|null
   */
  public function getHandler(): ?HttpHandlerInterface
  {
    return $this->handler;
  }

  /**
   * @param HttpHandlerInterface $handler
   *
   * @return $this
   */
  public function setHandler(HttpHandlerInterface $handler): HttpEvent
  {
    $this->handler = $handler;
    return $this;
  }

  /**
   * @return RequestEvent|ResponseEvent|null
   */
  public function getKernelEvent()
  {
    return $this->kernelEvent;
  }

  /**
   * @param KernelEvent $kernelEvent
   *
   * @return HttpEvent
   */
  public function setKernelEvent(KernelEvent $kernelEvent): HttpEvent
  {
    $this->kernelEvent = $kernelEvent;
    return $this;
  }

}