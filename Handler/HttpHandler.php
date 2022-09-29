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

use App\Entity\Austral\SecurityBundle\User;

use Austral\EntityBundle\EntityManager\EntityManager;

use Austral\ToolsBundle\Services\Debug;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\IdentityTranslator;

use \LogicException;

/**
 * Austral AdminHandler Abstract.
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class HttpHandler implements HttpHandlerInterface
{

  /**
   * @var ContainerInterface
   */
  protected ContainerInterface $container;

  /**
   * @var Request|null
   */
  protected ?Request $request;

  /**
   * @var DomainsManagement
   */
  protected DomainsManagement $domainsManagement;

  /**
   * @var EventDispatcherInterface
   */
  protected EventDispatcherInterface $dispatcher;

  /**
   * @var HttpTemplateParametersInterface
   */
  protected HttpTemplateParametersInterface $templateParameters;

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
   * @var Debug
   */
  protected Debug $debug;

  /**
   * Handler constructor.
   *
   * @param ContainerInterface $container
   * @param RequestStack $requestStack
   * @param EventDispatcherInterface $dispatcher
   * @param Debug $debug
   */
  public function __construct(ContainerInterface $container,
    RequestStack $requestStack,
    EventDispatcherInterface $dispatcher,
    Debug $debug
  )
  {
    $this->container = $container;
    $this->dispatcher = $dispatcher;
    $this->request = $requestStack->getCurrentRequest();
    $this->debug = $debug;
  }

  /**
   * Get request
   * @return Request|null
   */
  public function getRequest(): ?Request
  {
    return $this->request;
  }

  /**
   * @param Request|null $request
   *
   * @return $this
   */
  public function setRequest(?Request $request): HttpHandler
  {
    $this->request = $request;
    return $this;
  }

  /**
   * @return DomainsManagement
   */
  public function getDomainsManagement(): DomainsManagement
  {
    return $this->domainsManagement;
  }

  /**
   * @param DomainsManagement $domainsManagement
   *
   * @return HttpHandler
   */
  public function setDomainsManagement(DomainsManagement $domainsManagement): HttpHandler
  {
    $this->domainsManagement = $domainsManagement;
    return $this;
  }

  /**
   * Get dispatcher
   * @return EventDispatcherInterface
   */
  public function getDispatcher(): EventDispatcherInterface
  {
    return $this->dispatcher;
  }

  /**
   * @param EventDispatcherInterface $dispatcher
   *
   * @return HttpHandler
   */
  public function setDispatcher(EventDispatcherInterface $dispatcher): HttpHandler
  {
    $this->dispatcher = $dispatcher;
    return $this;
  }

  /**
   * @return EntityManager|null
   */
  public function getEntityManager(): ?EntityManager
  {
    /** @var EntityManager $entityManager */
    $entityManager = $this->container->get('austral.entity_manager');
    return $entityManager;
  }

  /**
   * @return string|null
   */
  public function getUserTabId(): ?string
  {
    return $this->userTabId;
  }

  /**
   * @param string|null $userTabId
   *
   * @return $this
   */
  public function setUserTabId(?string $userTabId): HttpHandler
  {
    $this->userTabId = $userTabId;
    return $this;
  }

  /**
   * @param $role
   *
   * @return bool
   */
  public function isGranted($role): bool
  {
    return $this->container->get('security.authorization_checker')->isGranted($role);
  }

  /**
   * @return User|string|null
   */
  public function getUser()
  {
    return $this->container->get('security.token_storage')->getToken()->getUser();
  }

  /**
   * @return HttpTemplateParametersInterface
   */
  public function getTemplateParameters() : HttpTemplateParametersInterface
  {
    return $this->templateParameters;
  }

  /**
   * @param HttpTemplateParametersInterface $templateParameters
   *
   * @return $this
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): HttpHandler
  {
    $this->templateParameters = $templateParameters;
    return $this;
  }

  /**
   * Returns a NotFoundHttpException.
   *
   * This will result in a 404 response code. Usage example:
   *
   *     throw $this->createNotFoundException('Page not found!');
   */
  public function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
  {
    return new NotFoundHttpException($message, $previous);
  }

  /**
   * @param string $message
   */
  public function pageNotFound(string $message = "The page not found !!!!")
  {
    throw $this->createNotFoundException($message);
  }

  /**
   * Generates a URL from the given parameters.
   *
   * @param string $route         The name of the route
   * @param mixed          $parameters    An array of parameters
   * @param bool|string    $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
   *
   * @return string The generated URL
   * @see UrlGeneratorInterface
   */
  public function generateUrl(string $route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, $env = null): string
  {
    return $this->container->get('router')->generate($route, $parameters, $referenceType);
  }

  /**
   * Adds a flash message to the current session for type.
   *
   * @throws LogicException
   */
  public function addFlash(string $type, $message): void
  {
    if (!$this->request->hasSession()) {
      throw new LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
    }
    $this->getSession()->getFlashBag()->add($type, $message);
  }

  /**
   * @return Session|null
   */
  public function getSession(): ?Session
  {
    return $this->request->getSession();
  }

  /**
   * @return object|IdentityTranslator|null
   */
  public function getTranslate()
  {
    return $this->container->get("translator");
  }

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  /**
   * @param string $redirectUrl
   *
   * @return $this
   */
  public function setRedirectUrl(string $redirectUrl): HttpHandler
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * @return int
   */
  public function getRedirectStatus(): ?int
  {
    return $this->redirectStatus;
  }

  /**
   * @param string $redirectStatus
   *
   * @return $this
   */
  public function setRedirectStatus(string $redirectStatus): HttpHandler
  {
    $this->redirectStatus = $redirectStatus;
    return $this;
  }

  /**
   * @return bool
   */
  public function isDevEnv(): bool
  {
    return $this->container->getParameter("kernel.environment") === "dev";
  }

}