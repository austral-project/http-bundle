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
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Austral Http Event Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpEventInterface
{

  /**
   * FormEvent constructor.
   *
   */
  public function __construct();

  /**
   * @return HttpHandlerInterface|null
   */
  public function getHandler(): ?HttpHandlerInterface;

  /**
   * @param HttpHandlerInterface $handler
   *
   * @return $this
   */
  public function setHandler(HttpHandlerInterface $handler): HttpEventInterface;

  /**
   * @return RequestEvent|ResponseEvent|ExceptionEvent|null
   */
  public function getKernelEvent();

  /**
   * @param KernelEvent $kernelEvent
   *
   * @return $this
   */
  public function setKernelEvent(KernelEvent $kernelEvent): HttpEventInterface;

}