<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Controller;

use Austral\HttpBundle\Controller\Interfaces\HttpControllerInterface;
use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\IdentityTranslator;


/**
 * Austral Admin Controller Abstract.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
abstract class HttpController implements HttpControllerInterface, ContainerAwareInterface
{
  use ContainerAwareTrait;

  /**
   * @var HttpHandlerInterface
   */
  protected HttpHandlerInterface $handlerManager;

  /**
   * @param HttpHandlerInterface $handlerManager
   *
   * @return HttpController
   */
  public function setHandlerManager(HttpHandlerInterface $handlerManager): HttpController
  {
    $this->handlerManager = $handlerManager;
    return $this;
  }

  /**
   * Returns true if the service id is defined.
   */
  protected function has(string $id): bool
  {
    return $this->container->has($id);
  }

  /**
   * Gets a container service by its id.
   *
   * @return object The service
   */
  protected function get(string $id): object
  {
    return $this->container->get($id);
  }

  /**
   * Get a user from the Security Token Storage.
   *
   * @return UserInterface|object|null
   *
   * @throws \LogicException If SecurityBundle is not available
   *
   * @see TokenInterface::getUser()
   */
  protected function getUser()
  {
    if (!$this->container->has('security.token_storage')) {
      throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
    }

    if (null === $token = $this->container->get('security.token_storage')->getToken()) {
      return null;
    }

    if (!\is_object($user = $token->getUser())) {
      return null;
    }

    return $user;
  }

  /**
   * Generates a URL from the given parameters.
   *
   * @see UrlGeneratorInterface
   */
  protected function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
  {
    return $this->container->get('router')->generate($route, $parameters, $referenceType);
  }

  /**
   * Forwards the request to another controller.
   *
   * @param string $controller The controller name (a string like Bundle\BlogBundle\Controller\PostController::indexAction)
   */
  protected function forward(string $controller, array $path = [], array $query = []): Response
  {
    $request = $this->container->get('request_stack')->getCurrentRequest();
    $path['_controller'] = $controller;
    $subRequest = $request->duplicate($query, null, $path);

    return $this->container->get('http_kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
  }

  /**
   * Returns a RedirectResponse to the given URL.
   */
  protected function redirect(string $url, int $status = 302): RedirectResponse
  {
    return new RedirectResponse($url, $status);
  }

  /**
   * Returns a RedirectResponse to the given route with the given parameters.
   */
  protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
  {
    return $this->redirect($this->generateUrl($route, $parameters), $status);
  }

  /**
   * @return Request|null
   */
  protected function getRequest(): ?Request
  {
    return $this->container->get("request_stack")->getCurrentRequest();
  }

  /**
   * @return SessionInterface|null
   */
  protected function getSession(): ?SessionInterface
  {
    if(!$this->getRequest())
    {
      return $this->getRequest()->getSession();
    }
    return null;
  }

  /**
   * Returns a rendered view.
   */
  protected function renderView(string $view, array $parameters = []): string
  {
    if($session = $this->getSession())
    {
      if($flashMessages = $session->getFlashBag()->all())
      {
        $parameters['flashMessages'] = $flashMessages;
        $session->getFlashBag()->clear();
      }
    }
    if (!$this->container->has('twig')) {
      throw new \LogicException('You can not use the "renderView" method if the Twig Bundle is not available. Try running "composer require symfony/twig-bundle".');
    }
    return $this->container->get('twig')->render($view, $parameters);
  }

  /**
   * @param string $view
   * @param array $parameters
   * @param Response|null $response
   *
   * @return Response
   */
  protected function render(string $view, array $parameters = [], Response $response = null): Response
  {
    $content = $this->renderView($view, $parameters);
    if (null === $response) {
      $response = new Response();
    }
    $response->setContent($content);
    return $response;
  }

  /**
   * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
   */
  protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
  {
    if ($this->container->has('serializer')) {
      $json = $this->container->get('serializer')->serialize($data, 'json', array_merge([
        'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
      ], $context));

      return new JsonResponse($json, $status, $headers, true);
    }

    return new JsonResponse($data, $status, $headers);
  }

  /**
   * @return object|IdentityTranslator|null
   */
  protected function getTranslate()
  {
    return $this->container->get("translator");
  }

  /**
   * Returns a NotFoundHttpException.
   *
   * This will result in a 404 response code. Usage example:
   *
   *     throw $this->createNotFoundException('Page not found!');
   */
  protected function createNotFoundException(string $message = 'Not Found', \Throwable $previous = null): NotFoundHttpException
  {
    return new NotFoundHttpException($message, $previous);
  }

  /**
   * Returns an AccessDeniedException.
   *
   * This will result in a 403 response code. Usage example:
   *
   *     throw $this->createAccessDeniedException('Unable to access this page!');
   *
   * @throws \LogicException If the Security component is not available
   */
  protected function createAccessDeniedException(string $message = 'Access Denied.', \Throwable $previous = null): AccessDeniedException
  {
    if (!class_exists(AccessDeniedException::class)) {
      throw new \LogicException('You can not use the "createAccessDeniedException" method if the Security component is not available. Try running "composer require symfony/security-bundle".');
    }

    return new AccessDeniedException($message, $previous);
  }

}