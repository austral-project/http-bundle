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

use Austral\HttpBundle\Handler\Interfaces\HttpHandlerInterface;
use Austral\HttpBundle\Template\Interfaces\HttpTemplateParametersInterface;

/**
 * Austral Http Controller Interface.
 *
 * @template T of object
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface HttpControllerInterface
{
  /**
   * @param HttpHandlerInterface<T> $handlerManager
   *
   * @return $this
   */
  public function setHandlerManager(HttpHandlerInterface $handlerManager): self;

  /**
   * @param HttpTemplateParametersInterface<T>|null $templateParameters
   *
   * @return $this
   */
  public function setTemplateParameters(?HttpTemplateParametersInterface $templateParameters = null): self;
}
