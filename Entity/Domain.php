<?php
/*
 * This file is part of the Austral Http Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Austral\HttpBundle\Entity;

use Austral\EntityFileBundle\Entity\Interfaces\CropperInterface;
use Austral\Contracts\File\FileInterface;
use Austral\EntityFileBundle\Entity\Traits\EntityFileCropperTrait;
use Austral\EntityFileBundle\Entity\Traits\EntityFileTrait;
use Austral\EntityFileBundle\Attribute as AustralFile;

use Austral\HttpBundle\Entity\Interfaces\DomainInterface;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\Traits\EntityTimestampableTrait;
use Austral\ToolsBundle\AustralTools;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Gedmo\Mapping\Annotation as Gedmo;
use Exception;


/**
 * Austral Domain Entity.
 * @template T of object
 *
 * @extends Entity<T>
 * @implements DomainInterface<T>
 * @implements FileInterface<T>
 * @implements CropperInterface<T>
 * @use EntityFileTrait<T>
 * @use EntityFileCropperTrait<T>
 *
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 */
#[ORM\MappedSuperclass]
abstract class Domain extends Entity implements DomainInterface, FileInterface, CropperInterface
{

  /** @use EntityFileTrait<T> */
  use EntityFileTrait;

  /** @use EntityFileCropperTrait<T> */
  use EntityFileCropperTrait;

  use EntityTimestampableTrait;

  /**
   * @var string
   */
  #[Orm\Column(name: 'id', type: 'string', length: 40)]
  #[ORM\Id()]
  protected string $id;

  /**
   * @var DomainInterface<T>|null
   */
  #[ORM\ManyToOne(
    targetEntity: \Austral\HttpBundle\Entity\Interfaces\DomainInterface::class,
    inversedBy: 'virtuals'
  )]
  #[ORM\JoinColumn(name: 'master', referencedColumnName: 'id', onDelete: 'SET NULL')]
  protected ?DomainInterface $master = null;

  /**
   * @var Collection<int, DomainInterface<T>>
   */
  #[ORM\OneToMany(
    targetEntity: \Austral\HttpBundle\Entity\Interfaces\DomainInterface::class,
    mappedBy: 'master',
    cascade: ['persist', 'remove'],
  )]
  protected Collection $virtuals;

  /**
   * @var string
   */
  #[ORM\Column(name: 'domain', type: 'string', length: 255, nullable: false)]
  protected string $domain = "";

  /**
   * @var string
   */
  #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
  protected string $name = "";

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'keyname', type: 'string', length: 255, nullable: false)]
  protected ?string $keyname = null;

  /**
   * @var string
   */
  #[ORM\Column(name: 'domain_env', type: 'string', length: 255, nullable: false, options: ["default" => "prod"])]
  protected string $domainEnv = "prod";

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'favicon', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize()]
  #[AustralFile\Croppers(croppers: [
    new AustralFile\Cropper(name: "favicon", picto: "austral-picto-globe", ratio: "1/1")
  ])]
  protected ?string $favicon = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'favicon_second', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize()]
  #[AustralFile\Croppers(croppers: [
    new AustralFile\Cropper(name: "favicon", picto: "austral-picto-globe", ratio: "1/1")
  ])]
  protected ?string $faviconSecond = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'logo', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize(widthMin: 0, heightMin: 0)]
  protected ?string $logo = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'logo_second', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize(widthMin: 0, heightMin: 0)]
  protected ?string $logoSecond = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'logo_email', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize(widthMin: 0, heightMin: 0)]
  protected ?string $logoEmail = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'picto', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize(widthMin: 0, heightMin: 0)]
  protected ?string $picto = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'picto_second', type: 'string', length: 255, nullable: true)]
  #[AustralFile\UploadParameters(configName: "page_image")]
  #[AustralFile\ImageSize(widthMin: 0, heightMin: 0)]
  protected ?string $pictoSecond = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'scheme', type: 'string', length: 255, nullable: true)]
  protected ?string $scheme = null;

  /**
   * @var boolean
   */
  #[ORM\Column(name: 'is_master', type: 'boolean', nullable: false, options: ["default" => false])]
  protected bool $isMaster = false;
  
  /**
   * @var boolean
   */
  #[ORM\Column(name: 'is_enabled', type: 'boolean', nullable: false, options: ["default" => false])]
  protected bool $isEnabled = false;

  /**
   * @var boolean
   */
  #[ORM\Column(name: 'is_virtual', type: 'boolean', nullable: false, options: ["default" => false])]
  protected bool $isVirtual = false;

  /**
   * @var boolean
   */
  #[ORM\Column(name: 'is_translate', type: 'boolean', nullable: false, options: ["default" => false])]
  protected bool $isTranslate = false;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'redirect_url', type: 'string', length: 255, nullable: true)]
  protected ?string $redirectUrl = null;

  /**
   * @var boolean
   */
  #[ORM\Column(name: 'redirect_with_uri', type: 'boolean', nullable: false, options: ["default" => false])]
  protected bool $redirectWithUri = false;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'contact_email', type: 'string', length: 255, nullable: true)]
  protected ?string $contactEmail = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'contact_phone', type: 'string', length: 255, nullable: true)]
  protected ?string $contactPhone = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'contact_name', type: 'string', length: 255, nullable: true)]
  protected ?string $contactName = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'contact_address', type: 'string', length: 255, nullable: true)]
  protected ?string $contactAddress = null;

  /**
   * @var boolean
   */
  #[ORM\Column(name: 'one_page', type: 'boolean', nullable: false, options: ["default" => false])]
  protected bool $onePage = false;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'language', type: 'string', length: 255, nullable: true)]
  protected ?string $language = null;

  /**
   * @var string|null
   */
  #[ORM\Column(name: 'email_from', type: 'string', length: 255, nullable: true)]
  protected ?string $emailFrom = null;

  /**
   * @var array<string, mixed>|null
   */
  #[ORM\Column(name: 'config_keys', type: 'json', nullable: true, options: ['default' => '{}'])]
  protected ?array $configKeys = [];

  /**
   * @var int<1, max>
   */
  #[Gedmo\SortablePosition]
  #[ORM\Column(name: 'position', type: 'integer', nullable: false, options: ["default" => 1])]
  protected int $position = 1;

  /**
   * Constructor
   * @throws Exception
   */
  public function __construct()
  {
    parent::__construct();
    $this->id = Uuid::uuid4()->toString();
    $this->virtuals = new ArrayCollection();
  }

  /**
   * urlParameterStatusName
   *
   * @return string
   */
  public function getUrlParameterStatusName(): string
  {
    return $this->name ?? $this->domain;
  }

  /**
   * @inheritDoc
   */
  public function __toString()
  {
    return $this->name ?? $this->domain;
  }

  /**
   * @inheritDoc
   */
  public function getMaster(): ?DomainInterface
  {
    return $this->master;
  }

  /**
   * @inheritDoc
   */
  public function setMaster(DomainInterface $master): self
  {
    $this->master = $master;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getVirtuals(): Collection
  {
    return $this->virtuals;
  }

  /**
   * @inheritDoc
   */
  public function setVirtuals(Collection $virtuals): self
  {
    $this->virtuals = $virtuals;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getIsTranslate(): bool
  {
    return $this->isTranslate;
  }

  /**
   * @inheritDoc
   */
  public function setIsTranslate(bool $isTranslate): self
  {
    $this->isTranslate = $isTranslate;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getDomainsByEnv(): array
  {
    if($this->getMaster() && !$this->getIsTranslate())
    {
      return $this->getMaster()->getDomainsByEnv();
    }

    $domainsByEnv = array();
    /** @var DomainInterface<T> $virtual */
    foreach($this->getVirtuals() as $virtual)
    {
      $domainsByEnv[$virtual->getDomainEnv()] = $virtual;
    }
    $domainsByEnv[$this->getDomainEnv()] = $this;
    return $domainsByEnv;
  }

  /**
   * @inheritDoc
   */
  public function getDomainByEnv(string $env): DomainInterface
  {
    if($this->getMaster() && !$this->getIsTranslate())
    {
      return $this->getMaster()->getDomainByEnv($env);
    }
    return AustralTools::valueByKey($this->getDomainsByEnv(), $env, $this);
  }

  /**
   * @inheritDoc
   */
  public function getDomain(): string
  {
    return $this->domain;
  }

  /**
   * @inheritDoc
   */
  public function setDomain(string $domain = ""): self
  {
    $this->domain = $domain;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @inheritDoc
   */
  public function setName(string $name = ""): self
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getKeyname(): ?string
  {
    return $this->keyname;
  }

  /**
   * @inheritDoc
   */
  public function setKeyname(string $keyname): self
  {
    $this->keyname = $this->keynameGenerator($keyname);
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getDomainEnv(): string
  {
    return $this->domainEnv;
  }

  /**
   * @inheritDoc
   */
  public function setDomainEnv(string $domainEnv): self
  {
    $this->domainEnv = $domainEnv;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getFavicon(): ?string
  {
    return $this->favicon;
  }

  /**
   * @inheritDoc
   */
  public function setFavicon(?string $favicon): self
  {
    $this->favicon = $favicon;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getLogo(): ?string
  {
    return $this->logo;
  }

  /**
   * @inheritDoc
   */
  public function setLogo(?string $logo): self
  {
    $this->logo = $logo;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getScheme(): string
  {
    return $this->scheme ?: self::SCHEME_HTTPS;
  }

  /**
   * @inheritDoc
   */
  public function setScheme(string $scheme): self
  {
    $this->scheme = $scheme;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getIsMaster(): bool
  {
    return $this->isMaster;
  }

  /**
   * @inheritDoc
   */
  public function setIsMaster(bool $isMaster): self
  {
    $this->isMaster = $isMaster;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getIsEnabled(): bool
  {
    return $this->isEnabled;
  }

  /**
   * @inheritDoc
   */
  public function setIsEnabled(bool $isEnabled): self
  {
    $this->isEnabled = $isEnabled;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getIsVirtual(): bool
  {
    return $this->isVirtual;
  }

  /**
   * @inheritDoc
   */
  public function setIsVirtual(bool $isVirtual): self
  {
    $this->isVirtual = $isVirtual;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getIsRedirect(): bool
  {
    return $this->getRedirectUrl() !== null;
  }

  /**
   * @inheritDoc
   */
  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  /**
   * @inheritDoc
   */
  public function setRedirectUrl(?string $redirectUrl): self
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getRedirectWithUri(): bool
  {
    return $this->redirectWithUri;
  }

  /**
   * @inheritDoc
   */
  public function setRedirectWithUri(bool $withUri): self
  {
    $this->redirectWithUri = $withUri;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getOnePage(): bool
  {
    return $this->onePage;
  }

  /**
   * @inheritDoc
   */
  public function setOnePage(bool $onePage): self
  {
    $this->onePage = $onePage;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getLanguage(): ?string
  {
    return $this->language;
  }

  /**
   * @inheritDoc
   */
  public function setLanguage(?string $language): Domain
  {
    $this->language = $language;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getPosition(): int
  {
    return $this->position;
  }

  /**
   * @inheritDoc
   */
  public function setPosition(int $position): self
  {
    $this->position = $position;
    return $this;
  }

  /**
   * @var string|null
   */
  protected ?string $requestLanguage = null;

  /**
   * @inheritDoc
   */
  public function getCurrentLanguage(): ?string
  {
    return $this->language ?? $this->requestLanguage;
  }

  /**
   * @inheritDoc
   */
  public function getRequestLanguage(): ?string
  {
    return $this->requestLanguage;
  }

  /**
   * @inheritDoc
   */
  public function setRequestLanguage(?string $requestLanguage = null): self
  {
    $this->requestLanguage = $requestLanguage;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getDomainsTranslate(): array
  {
    $domainsByTranslate = array();
    /** @var DomainInterface<T> $virtual */
    foreach($this->getVirtuals() as $virtual)
    {
      if($virtual->getIsTranslate() && !$this->getIsVirtual())
      {
        $domainsByTranslate[$virtual->getLanguage()] = $virtual;
      }
    }
    return $domainsByTranslate;
  }

  /**
   * @inheritDoc
   */
  public function getDomainTranslateByLanguage(?string $language = null): ?DomainInterface
  {
    return $language ? AustralTools::valueByKey($this->getDomainsTranslate(), $language) : null;
  }

  /**
   * @return array
   */
  public function getConfigKeys(): array
  {
    return $this->configKeys ?? array();
  }

  /**
   * @param array $configKeys
   *
   * @return $this
   */
  public function setConfigKeys(array $configKeys): self
  {
    $this->configKeys = $configKeys;
    return $this;
  }

  /**
   * getColorByKeyname
   *
   * @param string $keyname
   * @param mixed $default
   * @return mixed
   */
  public function getConfigKey(string $keyname, mixed $default = null): mixed
  {
    return array_key_exists($keyname, $this->getConfigKeys()) ? $this->configKeys[$keyname] : $default;
  }

  /**
   * setColorByKeyname
   *
   * @param string $keyname
   * @param mixed $value
   * @return $this
   */
  public function setConfigKey(string $keyname, mixed $value = null): self
  {
    $this->configKeys[$keyname] = $value;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFaviconSecond(): ?string
  {
    return $this->faviconSecond;
  }

  /**
   * @param string|null $faviconSecond
   *
   * @return $this
   */
  public function setFaviconSecond(?string $faviconSecond): self
  {
    $this->faviconSecond = $faviconSecond;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getLogoSecond(): ?string
  {
    return $this->logoSecond;
  }

  /**
   * @param string|null $logoSecond
   *
   * @return $this
   */
  public function setLogoSecond(?string $logoSecond): self
  {
    $this->logoSecond = $logoSecond;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getLogoEmail(): ?string
  {
    return $this->logoEmail;
  }

  /**
   * @param string|null $logoEmail
   *
   * @return $this
   */
  public function setLogoEmail(?string $logoEmail): self
  {
    $this->logoEmail = $logoEmail;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getPicto(): ?string
  {
    return $this->picto;
  }

  /**
   * @param string|null $picto
   *
   * @return $this
   */
  public function setPicto(?string $picto): self
  {
    $this->picto = $picto;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getPictoSecond(): ?string
  {
    return $this->pictoSecond;
  }

  /**
   * @param string|null $pictoSecond
   *
   * @return $this
   */
  public function setPictoSecond(?string $pictoSecond): self
  {
    $this->pictoSecond = $pictoSecond;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getContactEmail(): ?string
  {
    return $this->contactEmail;
  }

  /**
   * @param string|null $contactEmail
   *
   * @return $this
   */
  public function setContactEmail(?string $contactEmail): self
  {
    $this->contactEmail = $contactEmail;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getContactPhone(): ?string
  {
    return $this->contactPhone;
  }

  /**
   * @param string|null $contactPhone
   *
   * @return $this
   */
  public function setContactPhone(?string $contactPhone): self
  {
    $this->contactPhone = $contactPhone;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getContactName(): ?string
  {
    return $this->contactName;
  }

  /**
   * @param string|null $contactName
   *
   * @return $this
   */
  public function setContactName(?string $contactName): self
  {
    $this->contactName = $contactName;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getContactAddress(): ?string
  {
    return $this->contactAddress;
  }

  /**
   * @param string|null $contactAddress
   *
   * @return $this
   */
  public function setContactAddress(?string $contactAddress): self
  {
    $this->contactAddress = $contactAddress;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getEmailFrom(): ?string
  {
    return $this->emailFrom;
  }

  /**
   * @param string|null $emailFrom
   *
   * @return $this
   */
  public function setEmailFrom(?string $emailFrom): self
  {
    $this->emailFrom = $emailFrom;
    return $this;
  }
}
