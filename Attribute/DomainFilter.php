<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Austral\HttpBundle\Attribute;

use Austral\EntityBundle\Attribute\AustralEntityAttribute;

/**
 * Class Translate
 * @Attribute
 * @package Austral\HttpBundle\Attribute
 * @Target("CLASS")
 * @author  Matthieu Beurel <matthieu@austral.dev>
 * @final
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class DomainFilter extends AustralEntityAttribute
{

  /**
   * @var bool
   */
  public bool $autoDomainId = false;

  /**
   * @var bool
   */
  public bool $forAllDomainEnabled = false;

  /**
   * @var bool
   */
  public bool $autoAttachement = true;

  /**
   * @param bool $autoDomainId
   * @param bool $forAllDomainEnabled
   * @param bool $autoAttachement
   */
  public function __construct(bool $autoDomainId = false, bool $forAllDomainEnabled = false, bool $autoAttachement = true)
  {
    $this->autoDomainId = $autoDomainId;
    $this->forAllDomainEnabled = $forAllDomainEnabled;
    $this->autoAttachement = $autoAttachement;
  }

}
