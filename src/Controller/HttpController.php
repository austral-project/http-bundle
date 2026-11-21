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
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Austral Admin Controller Abstract.
 *
 * @template T of object
 *
 * @implements HttpControllerInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 *
 * @abstract
 */
abstract class HttpController implements HttpControllerInterface
{
  public function __construct(
    #[Autowire(service: 'service_container')] protected ContainerInterface $container,
    #[Autowire(service: 'router')] protected RouterInterface $router,
    #[Autowire(service: 'twig')] protected Environment $twig
  ) {
  }

  /**
   * @var HttpHandlerInterface<T>|null
   */
  protected ?HttpHandlerInterface $handlerManager = null;

  /**
   * {@inheritDoc}
   */
  public function setContainer(ContainerInterface $container): self
  {
    $this->container = $container;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function setHandlerManager(HttpHandlerInterface $handlerManager): self
  {
    $this->handlerManager = $handlerManager;

    return $this;
  }

  /**
   * @var HttpTemplateParametersInterface<T>|null
   */
  protected ?HttpTemplateParametersInterface $templateParameters = null;

  /**
   * {@inheritDoc}
   */
  public function setTemplateParameters(?HttpTemplateParametersInterface $templateParameters = null): self
  {
    $this->templateParameters = $templateParameters;

    return $this;
  }

  /**
   * has
   *
   * @param string $id
   *
   * @return bool
   */
  protected function has(string $id): bool
  {
    return $this->container->has($id);
  }

  /**
   * get
   *
   * @param string $id
   *
   * @return object|null
   */
  protected function get(string $id): ?object
  {
    return $this->container->get($id);
  }

  /**
   * generateUrl
   *
   * @param string               $route
   * @param array<string, mixed> $parameters
   * @param int                  $referenceType
   *
   * @return string
   */
  protected function generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
  {
    return $this->router->generate($route, $parameters, $referenceType);
  }

  /**
   * Returns a RedirectResponse to the given URL.
   */
  protected function redirect(string $url, int $status = 302): RedirectResponse
  {
    return new RedirectResponse($url, $status);
  }

  /**
   * redirectToRoute
   *
   * @param string               $route
   * @param array<string, mixed> $parameters
   * @param int                  $status
   *
   * @return RedirectResponse
   */
  protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
  {
    return $this->redirect($this->generateUrl($route, $parameters), $status);
  }

  /**
   * renderView
   *
   * @param string               $view
   * @param array<string, mixed> $parameters
   *
   * @return string
   *
   * @throws \Twig\Error\LoaderError
   * @throws \Twig\Error\RuntimeError
   * @throws \Twig\Error\SyntaxError
   */
  protected function renderView(string $view, array $parameters = []): string
  {
    return $this->twig->render($view, $parameters);
  }

  /**
   * render
   *
   * @param string               $view
   * @param array<string, mixed> $parameters
   * @param Response|null        $response
   *
   * @return Response
   *
   * @throws \Twig\Error\LoaderError
   * @throws \Twig\Error\RuntimeError
   * @throws \Twig\Error\SyntaxError
   */
  protected function render(string $view, array $parameters = [], ?Response $response = null): Response
  {
    $content = $this->renderView($view, $parameters);
    if (null === $response)
    {
      $response = new Response();
    }
    $response->setContent($content);

    return $response;
  }

  /**
   * json
   *
   * @param mixed                $data
   * @param int                  $status
   * @param array<string, mixed> $headers
   * @param bool                 $json
   *
   * @return JsonResponse
   */
  protected function json(mixed $data, int $status = 200, array $headers = [], bool $json = false): JsonResponse
  {
    return new JsonResponse($data, $status, $headers, $json);
  }
}
