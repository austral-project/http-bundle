<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\HttpBundle\Entity\Interfaces;

/**
 * Austral Domain Interface.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
interface DomainInterface
{

  const SCHEME_HTTPS = "https";
  const SCHEME_HTTP = "http";

  /**
   * @return string|null
   */
  public function getDomain(): ?string;

  /**
   * @param string|null $domain
   *
   * @return DomainInterface
   */
  public function setDomain(?string $domain): DomainInterface;

  /**
   * @return string|null
   */
  public function getName(): ?string;

  /**
   * @param string|null $name
   *
   * @return DomainInterface
   */
  public function setName(?string $name): DomainInterface;

  /**
   * @return string|null
   */
  public function getKeyname(): ?string;

  /**
   * @param string|null $keyname
   *
   * @return DomainInterface
   */
  public function setKeyname(?string $keyname): DomainInterface;

  /**
   * @return string|null
   */
  public function getFavicon(): ?string;

  /**
   * @param string|null $favicon
   *
   * @return $this
   */
  public function setFavicon(?string $favicon): DomainInterface;

  /**
   * @return string
   */
  public function getScheme(): string;

  /**
   * @param string $scheme
   *
   * @return DomainInterface
   */
  public function setScheme(string $scheme): DomainInterface;

  /**
   * @return bool
   */
  public function getIsMaster(): bool;

  /**
   * @param bool $isMaster
   *
   * @return DomainInterface
   */
  public function setIsMaster(bool $isMaster): DomainInterface;

  /**
   * @return bool
   */
  public function getIsEnabled(): bool;

  /**
   * @param bool $isEnabled
   *
   * @return DomainInterface
   */
  public function setIsEnabled(bool $isEnabled): DomainInterface;

  /**
   * @return bool
   */
  public function getIsVirtual(): bool;

  /**
   * @param bool $isVirtual
   *
   * @return $this
   */
  public function setIsVirtual(bool $isVirtual): DomainInterface;

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string;

  /**
   * @param string|null $redirectUrl
   *
   * @return DomainInterface
   */
  public function setRedirectUrl(?string $redirectUrl): DomainInterface;

  /**
   * @return bool
   */
  public function getOnePage(): bool;

  /**
   * @param bool $onePage
   *
   * @return $this
   */
  public function setOnePage(bool $onePage): DomainInterface;

  /**
   * @return string|null
   */
  public function getLanguage(): ?string;

  /**
   * @param string|null $language
   *
   * @return DomainInterface
   */
  public function setLanguage(?string $language): DomainInterface;

  /**
   * @return int|null
   */
  public function getPosition(): ?int;

  /**
   * @param int|null $position
   *
   * @return DomainInterface
   */
  public function setPosition(?int $position): DomainInterface;

  /**
   * @return string|null
   */
  public function getCurrentLanguage(): ?string;

  /**
   * @return string|null
   */
  public function getRequestLanguage(): ?string;

  /**
   * @param string|null $requestLanguage
   *
   * @return $this
   */
  public function setRequestLanguage(string $requestLanguage = null): DomainInterface;


}

    
    
      