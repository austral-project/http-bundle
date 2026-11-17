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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Austral Http Handler Interface.
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpHandlerInterface
{

  /**
   * @param RequestStack $request
   * @return $this
   */
  public function setRequestStack(RequestStack $request): self;

  /**
   * @param FormFactoryInterface $formFactory
   * @return $this
   */
  public function setFormFactory(FormFactoryInterface $formFactory): self;

  /**
   * @return FormFactoryInterface|null
   */
  public function getFormFactory(): ?FormFactoryInterface;

  /**
   * @return DomainsManagement<T>|null
   */
  public function getDomainsManagement(): ?DomainsManagement;

  /**
   * @param DomainsManagement<T> $domainsManagement
   * @return $this
   */
  public function setDomainsManagement(DomainsManagement $domainsManagement): self;

  /**
   * @return EventDispatcherInterface
   */
  public function getDispatcher(): EventDispatcherInterface;

  /**
   * @param mixed ...$args
   * @return void
   */
  public function executeHandlerMethod(mixed ...$args): void;

  /**
   * @return string|null
   */
  public function getUserTabId(): ?string;

  /**
   * @param string $userTabId
   * @return $this
   */
  public function setUserTabId(string $userTabId): self;

  /**
   * @param mixed $role
   * @return bool
   */
  public function isGranted(mixed $role): bool;

  /**
   * @return UserInterface<T>|null
   */
  public function getUser(): ?UserInterface;

  /**
   * @return HttpTemplateParametersInterface<T>|null
   */
  public function getTemplateParameters() : ?HttpTemplateParametersInterface;

  /**
   * @param HttpTemplateParametersInterface<T> $templateParameters
   * @return $this
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): self;

  /**
   * @param string $message
   * @param \Throwable|null $previous
   * @return NotFoundHttpException
   */
  public function createNotFoundException(string $message = 'Not Found', ?\Throwable $previous = null): NotFoundHttpException;

  /**
   * @param string $message
   */
  public function pageNotFound(string $message = "The page not found !!!!"): void;

  /**
   * @param string $route
   * @param array<string, int|string> $parameters
   * @param int $referenceType
   * @param string|null $env
   * @return string
   */
  public function generateUrl(string $route, array $parameters = array(), int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, ?string $env = null): string;

  /**
   * @param string $type
   * @param string $message
   * @return void
   * @throws \LogicException
   */
  public function addFlash(string $type, string $message): void;

  /**
   * @return SessionInterface|null
   */
  public function getSession(): ?SessionInterface;

  /**
   * @return TranslatorInterface
   */
  public function getTranslate(): TranslatorInterface;

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string;

  /**
   * @param string $redirectUrl
   * @return $this
   */
  public function setRedirectUrl(string $redirectUrl): self;

  /**
   * @return int
   */
  public function getRedirectStatus(): int;

  /**
   * @param int $redirectStatus
   * @return $this
   */
  public function setRedirectStatus(int $redirectStatus): self;

  /**
   * @return bool
   */
  public function isDevEnv(): bool;
}