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
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;


use Austral\SecurityBundle\Entity\Interfaces\UserInterface;
use Austral\ToolsBundle\Services\Debug;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use \LogicException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Austral AdminHandler Abstract.
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
   * @var DomainsManagement<T>|null
   */
  protected ?DomainsManagement $domainsManagement = null;

  /**
   * @var HttpTemplateParametersInterface<T>|null
   */
  protected ?HttpTemplateParametersInterface $templateParameters = null;

  /**
   * @var FormFactoryInterface|null
   */
  protected ?FormFactoryInterface $formFactory = null;

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
   * @var string|null
   */
  protected ?string $userTabId = null;

  /**
   * Handler constructor.
   *
   * @param ContainerInterface $container
   * @param EventDispatcherInterface $dispatcher
   * @param TokenStorageInterface $tokenStorage
   * @param AuthorizationCheckerInterface $authorizationChecker
   * @param TranslatorInterface $translator
   * @param RouterInterface $router
   * @param Debug|null $debug
   */
  public function __construct(
    #[Autowire(service: "service_container")] protected ContainerInterface $container,
    #[Autowire(service: 'event_dispatcher')] protected EventDispatcherInterface $dispatcher,
    #[Autowire(service: 'security.token_storage')] protected TokenStorageInterface $tokenStorage,
    #[Autowire(service: 'security.authorization_checker')] protected AuthorizationCheckerInterface $authorizationChecker,
    #[Autowire(service: 'translator')] protected TranslatorInterface $translator,
    #[Autowire(service: 'router')] protected RouterInterface $router,
    #[Autowire(service: 'austral.tools.debug')] protected ?Debug $debug,
  )
  {
  }

  /**
   * @inheritDoc
   */
  public function setRequestStack(RequestStack $request): self
  {
    $this->request = $request->getCurrentRequest();
    $this->session = $this->request?->getSession();
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function setFormFactory(FormFactoryInterface $formFactory): HttpHandler
  {
    $this->formFactory = $formFactory;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getFormFactory(): ?FormFactoryInterface
  {
    return $this->formFactory;
  }

  /**
   * @inheritDoc
   */
  public function getDomainsManagement(): ?DomainsManagement
  {
    return $this->domainsManagement;
  }

  /**
   * @inheritDoc
   */
  public function setDomainsManagement(DomainsManagement $domainsManagement): self
  {
    $this->domainsManagement = $domainsManagement;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getDispatcher(): EventDispatcherInterface
  {
    return $this->dispatcher;
  }

  /**
   * @inheritDoc
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
   * @return string|null
   */
  public function getUserTabId(): ?string
  {
    return $this->userTabId;
  }

  /**
   * @inheritDoc
   */
  public function setUserTabId(?string $userTabId): HttpHandler
  {
    $this->userTabId = $userTabId;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function isGranted(mixed $role): bool
  {
    return $this->authorizationChecker->isGranted($role);
  }

  /**
   * @inheritDoc
   */
  public function getUser(): ?UserInterface
  {
    $user = $this->tokenStorage->getToken()?->getUser();
    if($user instanceof UserInterface)
    {
      return $user;
    }
    return null;
  }

  /**
   * @inheritDoc
   */
  public function getTemplateParameters() : ?HttpTemplateParametersInterface
  {
    return $this->templateParameters;
  }

  /**
   * @inheritDoc
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): self
  {
    $this->templateParameters = $templateParameters;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function createNotFoundException(string $message = 'Not Found', ?\Throwable $previous = null): NotFoundHttpException
  {
    return new NotFoundHttpException($message, $previous);
  }

  /**
   * @inheritDoc
   */
  public function pageNotFound(string $message = "The page not found !!!!"): void
  {
    throw $this->createNotFoundException($message);
  }

  /**
   * @inheritDoc
   */
  public function generateUrl(string $route, array $parameters = array(), int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, ?string $env = null): string
  {
    return $this->router->generate($route, $parameters, $referenceType);
  }

  /**
   * @inheritDoc
   */
  public function addFlash(string $type, string $message): void
  {
    if (!$this->session) {
      throw new LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
    }
    if($this->session instanceof Session)
    {
      $this->session->getFlashBag()->add($type, $message);
    }
  }

  /**
   * @inheritDoc
   */
  public function getSession(): ?SessionInterface
  {
    return $this->session;
  }

  /**
   * @inheritDoc
   */
  public function getTranslate(): TranslatorInterface
  {
    return $this->translator;
  }

  /**
   * @inheritDoc
   */
  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  /**
   * @inheritDoc
   */
  public function setRedirectUrl(string $redirectUrl): self
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getRedirectStatus(): int
  {
    return $this->redirectStatus;
  }

  /**
   * @inheritDoc
   */
  public function setRedirectStatus(int $redirectStatus): self
  {
    $this->redirectStatus = $redirectStatus;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function isDevEnv(): bool
  {
    return $this->container->getParameter("kernel.environment") === "dev";
  }

}