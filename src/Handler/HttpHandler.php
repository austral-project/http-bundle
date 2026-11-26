<?php

/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Handler;

use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Austral\ToolsBundle\Services\Debug;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Austral Http Handler Abstract.
 *
 * Shared service without state of the request: the request and the session are read from the RequestStack when
 * they are used; the template parameters, the handler method and the redirection are forgotten by reset()
 * (kernel.reset tag on the service of the handler).
 *
 * @template T of object
 *
 * @implements HttpHandlerInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class HttpHandler implements HttpHandlerInterface
{
  /**
   * @var HttpTemplateParametersInterface<T>|null
   */
  protected ?HttpTemplateParametersInterface $templateParameters = null;

  /**
   * @var string|null
   */
  public ?string $handlerMethod = null;

  /**
   * @var string|null
   */
  protected ?string $redirectUrl = null;

  /**
   * @var int
   */
  protected int $redirectStatus = 301;

  /**
   * __construct
   *
   * @param EventDispatcherInterface $dispatcher
   * @param TranslatorInterface      $translator
   * @param RouterInterface          $router
   * @param RequestStack             $requestStack
   * @param string                   $environment
   * @param Debug|null               $debug
   *
   * @return void
   */
  public function __construct(
    protected EventDispatcherInterface $dispatcher,
    protected TranslatorInterface $translator,
    protected RouterInterface $router,
    protected RequestStack $requestStack,
    protected string $environment,
    protected ?Debug $debug,
  ) {
  }

  /**
   * {@inheritDoc}
   */
  public function getRequest(): ?Request
  {
    return $this->requestStack->getCurrentRequest();
  }

  /**
   * {@inheritDoc}
   */
  public function getDispatcher(): EventDispatcherInterface
  {
    return $this->dispatcher;
  }

  /**
   * {@inheritDoc}
   */
  public function executeHandlerMethod(mixed ...$args): void
  {
    if (
      is_string($this->handlerMethod)
      && is_callable([$this, $this->handlerMethod])
    ) {
      $method = $this->handlerMethod;
      $this->$method(...$args);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getTemplateParameters(): ?HttpTemplateParametersInterface
  {
    return $this->templateParameters;
  }

  /**
   * {@inheritDoc}
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): self
  {
    $this->templateParameters = $templateParameters;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function createNotFoundException(string $message = 'Not Found', ?\Throwable $previous = null): NotFoundHttpException
  {
    return new NotFoundHttpException($message, $previous);
  }

  /**
   * {@inheritDoc}
   */
  public function pageNotFound(string $message = 'The page not found !!!!'): void
  {
    throw $this->createNotFoundException($message);
  }

  /**
   * {@inheritDoc}
   */
  public function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, ?string $env = null): string
  {
    return $this->router->generate($route, $parameters, $referenceType);
  }

  /**
   * {@inheritDoc}
   */
  public function addFlash(string $type, string $message): void
  {
    $session = $this->getSession();
    if (!$session)
    {
      throw new \LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
    }
    if ($session instanceof Session)
    {
      $session->getFlashBag()->add($type, $message);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getSession(): ?SessionInterface
  {
    $request = $this->getRequest();

    return $request?->hasSession() ? $request->getSession() : null;
  }

  /**
   * {@inheritDoc}
   */
  public function getTranslate(): TranslatorInterface
  {
    return $this->translator;
  }

  /**
   * {@inheritDoc}
   */
  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  /**
   * {@inheritDoc}
   */
  public function setRedirectUrl(string $redirectUrl): self
  {
    $this->redirectUrl = $redirectUrl;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getRedirectStatus(): int
  {
    return $this->redirectStatus;
  }

  /**
   * {@inheritDoc}
   */
  public function setRedirectStatus(int $redirectStatus): self
  {
    $this->redirectStatus = $redirectStatus;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function isDevEnv(): bool
  {
    return 'dev' === $this->environment;
  }

  /**
   * {@inheritDoc}
   */
  public function reset(): void
  {
    $this->templateParameters = null;
    $this->handlerMethod = null;
    $this->redirectUrl = null;
    $this->redirectStatus = 301;
  }
}
