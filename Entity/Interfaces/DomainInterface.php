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

use Austral\Contracts\Entity\EntityInterface;
use Doctrine\Common\Collections\Collection;

/**
 * Austral Domain Interface.
 * @template T of object
 *
 * @extends EntityInterface<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @interface
 */
interface DomainInterface extends EntityInterface
{

  const string SCHEME_HTTPS = "https";
  const string SCHEME_HTTP = "http";

  /**
   * @return DomainInterface<T>|null
   */
  public function getMaster(): ?DomainInterface;

  /**
   * @param DomainInterface<T> $master
   *
   * @return $this
   */
  public function setMaster(DomainInterface $master): self;

  /**
   * @return Collection<int, DomainInterface<T>>
   */
  public function getVirtuals(): Collection;

  /**
   * @param Collection<int, DomainInterface<T>> $virtuals
   *
   * @return $this
   */
  public function setVirtuals(Collection $virtuals): self;

  /**
   * @return string
   */
  public function getDomain(): string;

  /**
   * @param string $domain
   *
   * @return $this
   */
  public function setDomain(string $domain = ""): self;

  /**
   * @return string
   */
  public function getName(): string;

  /**
   * @param string $name
   *
   * @return $this
   */
  public function setName(string $name = ""): self;

  /**
   * @return string|null
   */
  public function getKeyname(): ?string;

  /**
   * @param string $keyname
   *
   * @return $this
   */
  public function setKeyname(string $keyname): self;

  /**
   * @return string
   */
  public function getDomainEnv(): string;

  /**
   * @param string $domainEnv
   * @return $this
   */
  public function setDomainEnv(string $domainEnv): self;

  /**
   * @return array<string, DomainInterface<T>>
   */
  public function getDomainsByEnv(): array;

  /**
   * @param string $env
   * @return DomainInterface<T>
   */
  public function getDomainByEnv(string $env): DomainInterface;

  /**
   * getFavicon
   *
   * @return string|null
   */
  public function getFavicon(): ?string;

  /**
   * @param string|null $favicon
   * @return $this
   */
  public function setFavicon(?string $favicon): self;

  /**
   * getFaviconSecond
   *
   * @return string|null
   */
  public function getFaviconSecond(): ?string;

  /**
   * @param string|null $faviconSecond
   * @return $this
   */
  public function setFaviconSecond(?string $faviconSecond): DomainInterface;

  /**
   * getLogo
   *
   * @return string|null
   */
  public function getLogo(): ?string;

  /**
   * @param string|null $logo
   * @return $this
   */
  public function setLogo(?string $logo): DomainInterface;

  /**
   * getLogoSecond
   *
   * @return string|null
   */
  public function getLogoSecond(): ?string;

  /**
   * @param string|null $logoSecond
   * @return $this
   */
  public function setLogoSecond(?string $logoSecond): DomainInterface;

  /**
   * getLogoEmail
   *
   * @return string|null
   */
  public function getLogoEmail(): ?string;

  /**
   * @param string|null $logoEmail
   * @return $this
   */
  public function setLogoEmail(?string $logoEmail): DomainInterface;

  /**
   * getPicto
   *
   * @return string|null
   */
  public function getPicto(): ?string;

  /**
   * @param string|null $picto
   * @return $this
   */
  public function setPicto(?string $picto): DomainInterface;

  /**
   * getPictoSecond
   *
   * @return string|null
   */
  public function getPictoSecond(): ?string;

  /**
   * @param string|null $pictoSecond
   * @return $this
   */
  public function setPictoSecond(?string $pictoSecond): DomainInterface;

  /**
   * @return string
   */
  public function getScheme(): string;

  /**
   * @param string $scheme
   *
   * @return $this
   */
  public function setScheme(string $scheme): self;

  /**
   * @return bool
   */
  public function getIsMaster(): bool;

  /**
   * @param bool $isMaster
   *
   * @return $this
   */
  public function setIsMaster(bool $isMaster): self;

  /**
   * @return bool
   */
  public function getIsEnabled(): bool;

  /**
   * @param bool $isEnabled
   *
   * @return $this
   */
  public function setIsEnabled(bool $isEnabled): self;

  /**
   * @return bool
   */
  public function getIsVirtual(): bool;

  /**
   * @param bool $isVirtual
   *
   * @return $this
   */
  public function setIsVirtual(bool $isVirtual): self;

  /**
   * @return bool
   */
  public function getIsTranslate(): bool;

  /**
   * @param bool $isTranslate
   *
   * @return $this
   */
  public function setIsTranslate(bool $isTranslate): self;

  /**
   * @return bool
   */
  public function getIsRedirect(): bool;

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string;

  /**
   * @param string|null $redirectUrl
   *
   * @return $this
   */
  public function setRedirectUrl(?string $redirectUrl): self;

  /**
   * @return bool
   */
  public function getRedirectWithUri(): bool;

  /**
   * @param bool $withUri
   *
   * @return $this
   */
  public function setRedirectWithUri(bool $withUri): self;

  /**
   * getContactEmail
   *
   * @return string|null
   */
  public function getContactEmail(): ?string;

  /**
   * @param string|null $contactEmail
   * @return $this
   */
  public function setContactEmail(?string $contactEmail): DomainInterface;

  /**
   * getContactPhone
   *
   * @return string|null
   */
  public function getContactPhone(): ?string;

  /**
   * @param string|null $contactPhone
   * @return $this
   */
  public function setContactPhone(?string $contactPhone): DomainInterface;

  /**
   * getContactName
   *
   * @return string|null
   */
  public function getContactName(): ?string;
  /**
   * @param string|null $contactName
   * @return $this
   */
  public function setContactName(?string $contactName): DomainInterface;

  /**
   * getContactAddress
   *
   * @return string|null
   */
  public function getContactAddress(): ?string;

  /**
   * @param string|null $contactAddress
   * @return $this
   */
  public function setContactAddress(?string $contactAddress): DomainInterface;

  /**
   * @return bool
   */
  public function getOnePage(): bool;

  /**
   * @param bool $onePage
   *
   * @return $this
   */
  public function setOnePage(bool $onePage): self;

  /**
   * @return string|null
   */
  public function getLanguage(): ?string;

  /**
   * @param string|null $language
   *
   * @return $this
   */
  public function setLanguage(?string $language): self;

  /**
   * @return int<1, max>
   */
  public function getPosition(): int;

  /**
   * @param int<1, max> $position
   *
   * @return $this
   */
  public function setPosition(int $position): self;

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
  public function setRequestLanguage(?string $requestLanguage = null): self;

  /**
   * @return array<string, DomainInterface<T>>
   */
  public function getDomainsTranslate(): array;

  /**
   * @param string|null $language
   * @return DomainInterface<T>|null
   */
  public function getDomainTranslateByLanguage(?string $language = null): ?DomainInterface;

  /**
   * @param string $keyname
   * @param mixed $default
   * @return mixed
   */
  public function getConfigKey(string $keyname, mixed $default = null): mixed;

  /**
   * @param string $keyname
   * @param mixed $value
   * @return $this
   */
  public function setConfigKey(string $keyname, mixed $value = null): self;

}

    
    
      