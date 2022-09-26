<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Austral\HttpBundle\Annotation;

use Austral\EntityBundle\Annotation\AustralEntityAnnotation;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS"})
 */
final class DomainFilter extends AustralEntityAnnotation
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
  public function __construct(bool $autoDomainId = false, bool $forAllDomainEnabled = false, $autoAttachement = true)
  {
    $this->autoDomainId = $autoDomainId;
    $this->forAllDomainEnabled = $forAllDomainEnabled;
    $this->autoAttachement = $autoAttachement;
  }

}
