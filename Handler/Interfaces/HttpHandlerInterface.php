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

use Austral\HttpBundle\Services\DomainsManagement;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;

use Austral\EntityBundle\EntityManager\EntityManagerInterface;
use Austral\SecurityBundle\Entity\Interfaces\UserInterface;
use Austral\ToolsBundle\Services\Debug;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\IdentityTranslator;

/**
 * Austral Http Handler Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpHandlerInterface
{

  /**
   * AdminHandlerInterface constructor.
   *
   * @param RequestStack $request
   * @param EventDispatcherInterface $dispatcher
   * @param TokenStorageInterface $tokenStorage
   * @param Debug|null $debug
   */
  public function __construct(RequestStack $request,
    EventDispatcherInterface $dispatcher,
    TokenStorageInterface $tokenStorage,
    ?Debug $debug
  );

  /**
   * setContainer
   *
   * @param ContainerInterface $container
   * @return $this
   */
  public function setContainer(ContainerInterface $container): HttpHandlerInterface;

  /**
   * Get request
   * @return Request|null
   */
  public function getRequest(): ?Request;
  /**
   * @param Request|null $request
   *
   * @return $this
   */
  public function setRequest(?Request $request): HttpHandlerInterface;

  /**
   * @return DomainsManagement
   */
  public function getDomainsManagement(): DomainsManagement;

  /**
   * @param DomainsManagement $domainsManagement
   *
   * @return HttpHandlerInterface
   */
  public function setDomainsManagement(DomainsManagement $domainsManagement): HttpHandlerInterface;

  /**
   * Get dispatcher
   * @return EventDispatcherInterface
   */
  public function getDispatcher(): EventDispatcherInterface;

  /**
   * @param EventDispatcherInterface $dispatcher
   *
   * @return $this
   */
  public function setDispatcher(EventDispatcherInterface $dispatcher): HttpHandlerInterface;

  /**
   * executeHandlerMethod
   */
  public function executeHandlerMethod();

  /**
   * @return EntityManagerInterface|null
   */
  public function getEntityManager(): ?EntityManagerInterface;

  /**
   * @return string|null
   */
  public function getUserTabId(): ?string;

  /**
   * @param string|null $userTabId
   *
   * @return $this
   */
  public function setUserTabId(?string $userTabId): HttpHandlerInterface;

  /**
   * @param $role
   *
   * @return bool
   */
  public function isGranted($role): bool;

  /**
   * @return UserInterface|string|null
   */
  public function getUser();

  /**
   * @return HttpTemplateParametersInterface
   */
  public function getTemplateParameters() : HttpTemplateParametersInterface;

  /**
   * @param HttpTemplateParametersInterface $templateParameters
   *
   * @return $this
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): HttpHandlerInterface;

  /**
   * Returns a NotFoundHttpException.
   *
   * This will result in a 404 response code. Usage example:
   *
   *     throw $this->createNotFoundException('Page not found!');
   */
  public function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException;

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
  public function generateUrl(string $route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, $env = null): string;
  /**
   * Adds a flash message to the current session for type.
   *
   * @throws \LogicException
   */
  public function addFlash(string $type, $message): void;

  /**
   * @return SessionInterface|null
   */
  public function getSession(): ?SessionInterface;

  /**
   * @return object|IdentityTranslator|null
   */
  public function getTranslate();

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string;

  /**
   * @return bool
   */
  public function isDevEnv(): bool;
}