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
 * Austral AdminHandler Abstract.
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
   * @var Request|null
   */
  protected ?Request $request = null;

  /**
   * @var SessionInterface|null
   */
  protected ?SessionInterface $session = null;

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
   * Handler constructor.
   *
   * @param EventDispatcherInterface $dispatcher
   * @param TranslatorInterface      $translator
   * @param RouterInterface          $router
   * @param string                   $environment
   * @param Debug|null               $debug
   */
  public function __construct(
    protected EventDispatcherInterface $dispatcher,
    protected TranslatorInterface $translator,
    protected RouterInterface $router,
    protected string $environment,
    protected ?Debug $debug,
  ) {
  }

  /**
   * {@inheritDoc}
   */
  public function setRequestStack(RequestStack $request): self
  {
    $this->request = $request->getCurrentRequest();
    $this->session = $this->request?->getSession();

    return $this;
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
    if (!$this->session)
    {
      throw new \LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
    }
    if ($this->session instanceof Session)
    {
      $this->session->getFlashBag()->add($type, $message);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getSession(): ?SessionInterface
  {
    return $this->session;
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
}
