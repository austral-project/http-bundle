<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Handler\Interfaces;

use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Austral Http Handler Interface.
 *
 * A handler is a shared service: it reads the request from the RequestStack when it is called and forgets the
 * state of a request (template parameters, handler method, redirection) in reset().
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpHandlerInterface extends ResetInterface
{
  /**
   * getRequest
   * The current request of the RequestStack.
   *
   * @return Request|null
   */
  public function getRequest(): ?Request;

  /**
   * getDispatcher
   *
   * @return EventDispatcherInterface
   */
  public function getDispatcher(): EventDispatcherInterface;

  /**
   * executeHandlerMethod
   *
   * @param mixed ...$args
   *
   * @return void
   */
  public function executeHandlerMethod(mixed ...$args): void;

  /**
   * getTemplateParameters
   *
   * @return HttpTemplateParametersInterface<T>|null
   */
  public function getTemplateParameters(): ?HttpTemplateParametersInterface;

  /**
   * setTemplateParameters
   *
   * @param HttpTemplateParametersInterface<T> $templateParameters
   *
   * @return $this
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): self;

  /**
   * createNotFoundException
   *
   * @param string          $message
   * @param \Throwable|null $previous
   *
   * @return NotFoundHttpException
   */
  public function createNotFoundException(string $message = 'Not Found', ?\Throwable $previous = null): NotFoundHttpException;

  /**
   * pageNotFound
   *
   * @param string $message
   *
   * @return void
   *
   * @throws NotFoundHttpException always
   */
  public function pageNotFound(string $message = 'The page not found !!!!'): void;

  /**
   * generateUrl
   *
   * @param string                    $route
   * @param array<string, int|string> $parameters
   * @param int                       $referenceType
   * @param string|null               $env
   *
   * @return string
   */
  public function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, ?string $env = null): string;

  /**
   * addFlash
   *
   * @param string $type
   * @param string $message
   *
   * @return void
   *
   * @throws \LogicException
   */
  public function addFlash(string $type, string $message): void;

  /**
   * getSession
   *
   * @return SessionInterface|null
   */
  public function getSession(): ?SessionInterface;

  /**
   * getTranslate
   *
   * @return TranslatorInterface
   */
  public function getTranslate(): TranslatorInterface;

  /**
   * getRedirectUrl
   *
   * @return string|null
   */
  public function getRedirectUrl(): ?string;

  /**
   * setRedirectUrl
   *
   * @param string $redirectUrl
   *
   * @return $this
   */
  public function setRedirectUrl(string $redirectUrl): self;

  /**
   * getRedirectStatus
   *
   * @return int
   */
  public function getRedirectStatus(): int;

  /**
   * setRedirectStatus
   *
   * @param int $redirectStatus
   *
   * @return $this
   */
  public function setRedirectStatus(int $redirectStatus): self;

  /**
   * isDevEnv
   *
   * @return bool
   */
  public function isDevEnv(): bool;
}
