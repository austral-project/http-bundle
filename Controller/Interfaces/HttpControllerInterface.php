<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Controller\Interfaces;

use Austral\HttpBundle\Controller\HttpController;
use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Austral Http Controller Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpControllerInterface
{

  /**
   * @param ContainerInterface|null $container
   */
  public function setContainer(ContainerInterface $container = null);

  /**
   * @param HttpHandlerInterface $handlerManager
   *
   * @return HttpControllerInterface
   */
  public function setHandlerManager(HttpHandlerInterface  $handlerManager): HttpControllerInterface;

  /**
   * @param HttpTemplateParametersInterface $templateParameters
   *
   * @return HttpControllerInterface
   */
  public function setTemplateParameters(HttpTemplateParametersInterface $templateParameters): HttpControllerInterface;

}