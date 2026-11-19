<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Configuration;

use Austral\ToolsBundle\Configuration\BaseConfiguration;

/**
 * Austral Http Configuration.
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
Class HttpConfiguration extends BaseConfiguration
{

  /**
   * @var string|null
   */
  protected ?string $prefix = "http";

  /**
   * @var int|null
   */
  protected ?int $niveauMax = null;

}