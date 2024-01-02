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

use Austral\EntityBundle\Entity\Interfaces\FileInterface;
use Austral\EntityFileBundle\Entity\Traits\EntityFileCropperTrait;
use Austral\EntityFileBundle\Entity\Traits\EntityFileTrait;
use Austral\HttpBundle\Entity\Interfaces\DomainInterface;
use Austral\EntityFileBundle\Annotation as AustralFile;

use Austral\EntityBundle\Entity\Entity;
use Austral\EntityBundle\Entity\EntityInterface;
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
 * @author Matthieu Beurel <matthieu@austral.dev>
 * @abstract
 * @ORM\MappedSuperclass
 */
abstract class Domain extends Entity implements DomainInterface, EntityInterface, FileInterface
{

  use EntityFileTrait;
  use EntityFileCropperTrait;
  use EntityTimestampableTrait;

  /**
   * @var string
   * @ORM\Column(name="id", type="string", length=40)
   * @ORM\Id
   */
  protected $id;
  
  /**
   * @var DomainInterface|null
   * @ORM\ManyToOne(targetEntity="Austral\HttpBundle\Entity\Interfaces\DomainInterface", inversedBy="virtuals")
   * @ORM\JoinColumn(name="master", referencedColumnName="id", onDelete="SET NULL")
   */
  protected ?DomainInterface $master = null;

  /**
   * @ORM\OneToMany(targetEntity="Austral\HttpBundle\Entity\Interfaces\DomainInterface", mappedBy="master", cascade={"persist", "remove"})
   */
  protected Collection $virtuals;

  /**
   * @var string|null
   * @ORM\Column(name="domain", type="string", length=255, nullable=false )
   */
  protected ?string $domain = null;

  /**
   * @var string|null
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   */
  protected ?string $name = null;

  /**
   * @var string|null
   * @ORM\Column(name="keyname", type="string", length=255, nullable=false)
   */
  protected ?string $keyname = null;

  /**
   * @var string|null
   * @ORM\Column(name="domain_env", type="string", length=255, nullable=false, options={"default": "prod"})
   */
  protected string $domainEnv = "prod";

  /**
   * @var string|null
   * @ORM\Column(name="favicon", type="string", length=255, nullable=true )
   * @AustralFile\UploadParameters(configName="page_image")
   * @AustralFile\ImageSize()
   * @AustralFile\Croppers(croppers={
   *   @AustralFile\Cropper(name="logo", ratio="1/1", picto="austral-picto-globe" )
   * })
   */
  protected ?string $favicon = null;

  /**
   * @var string|null
   * @ORM\Column(name="logo", type="string", length=255, nullable=true)
   * @AustralFile\UploadParameters(configName="page_image")
   * @AustralFile\ImageSize(widthMin="0", heightMin="0")
   */
  protected ?string $logo = null;

  /**
   * @var string|null
   * @ORM\Column(name="scheme", type="string", length=255, nullable=true )
   */
  protected ?string $scheme = null;

  /**
   * @var boolean
   * @ORM\Column(name="is_master", type="boolean", nullable=true)
   */
  protected bool $isMaster = false;
  
  /**
   * @var boolean
   * @ORM\Column(name="is_enabled", type="boolean", nullable=true)
   */
  protected bool $isEnabled = false;

  /**
   * @var boolean
   * @ORM\Column(name="is_virtual", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $isVirtual = false;

  /**
   * @var boolean
   * @ORM\Column(name="is_translate", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $isTranslate = false;

  /**
   * @var string|null
   * @ORM\Column(name="redirect_url", type="string", length=255, nullable=true )
   */
  protected ?string $redirectUrl = null;

  /**
   * @var boolean
   * @ORM\Column(name="redirect_with_uri", type="boolean", nullable=false, options={"default": false})
   */
  protected bool $redirectWithUri = false;

  /**
   * @var boolean
   * @ORM\Column(name="one_page", type="boolean", nullable=true, options={"default": false})
   */
  protected bool $onePage = false;

  /**
   * @var string|null
   * @ORM\Column(name="language", type="string", length=255, nullable=true )
   */
  protected ?string $language = null;

  /**
   * @var int
   * @Gedmo\SortablePosition
   * @ORM\Column(name="position", type="integer", nullable=false, options={"default": 1} )
   */
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
   * @return string
   */
  public function __toString()
  {
    return $this->name ?? $this->domain;
  }

  /**
   * @return DomainInterface|null
   */
  public function getMaster(): ?DomainInterface
  {
    return $this->master;
  }

  /**
   * @param DomainInterface $master
   *
   * @return Domain
   */
  public function setMaster(DomainInterface $master): Domain
  {
    $this->master = $master;
    return $this;
  }

  /**
   * @return Collection
   */
  public function getVirtuals(): Collection
  {
    return $this->virtuals;
  }

  /**
   * @param Collection $virtuals
   *
   * @return Domain
   */
  public function setVirtuals(Collection $virtuals): Domain
  {
    $this->virtuals = $virtuals;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsTranslate(): bool
  {
    return $this->isTranslate;
  }

  /**
   * @param bool $isTranslate
   *
   * @return $this
   */
  public function setIsTranslate(bool $isTranslate): Domain
  {
    $this->isTranslate = $isTranslate;
    return $this;
  }

  /**
   * getDomainsByEnv
   *
   * @return array
   */
  public function getDomainsByEnv(): array
  {
    if($this->getMaster() && !$this->getIsTranslate())
    {
      return $this->getMaster()->getDomainsByEnv();
    }
    $domainsByEnv = array();
    /** @var DomainInterface $virtual */
    foreach($this->getVirtuals() as $virtual)
    {
      $domainsByEnv[$virtual->getDomainEnv()] = $virtual;
    }
    $domainsByEnv[$this->getDomainEnv()] = $this;
    return $domainsByEnv;
  }

  /**
   * getDomainByEnv
   *
   * @param string $env
   * @return DomainInterface
   */
  public function getDomainByEnv(string $env): DomainInterface
  {
    if($this->getMaster() && !$this->getIsTranslate())
    {
      return $this->getMaster()->getDomainByEnv($env);
    }
    return AustralTools::getValueByKey($this->getDomainsByEnv(), $env, $this);
  }

  /**
   * @return string|null
   */
  public function getDomain(): ?string
  {
    return $this->domain;
  }

  /**
   * @param string|null $domain
   *
   * @return Domain
   */
  public function setDomain(?string $domain): Domain
  {
    $this->domain = $domain;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * @param string|null $name
   *
   * @return $this
   */
  public function setName(?string $name): Domain
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getKeyname(): ?string
  {
    return $this->keyname;
  }

  /**
   * @param string|null $keyname
   *
   * @return Domain
   */
  public function setKeyname(?string $keyname): Domain
  {
    $this->keyname = $this->keynameGenerator($keyname);
    return $this;
  }

  /**
   * getDomainEnv
   *
   * @return string
   */
  public function getDomainEnv(): string
  {
    return $this->domainEnv;
  }

  /**
   * setDomainEnv
   *
   * @param string $domainEnv
   * @return $this
   */
  public function setDomainEnv(string $domainEnv): Domain
  {
    $this->domainEnv = $domainEnv;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getFavicon(): ?string
  {
    return $this->favicon;
  }

  /**
   * @param string|null $favicon
   *
   * @return $this
   */
  public function setFavicon(?string $favicon): Domain
  {
    $this->favicon = $favicon;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getLogo(): ?string
  {
    return $this->logo;
  }

  /**
   * @param string|null $logo
   *
   * @return Domain
   */
  public function setLogo(?string $logo): Domain
  {
    $this->logo = $logo;
    return $this;
  }

  /**
   * @return string
   */
  public function getScheme(): string
  {
    return $this->scheme ?: self::SCHEME_HTTPS;
  }

  /**
   * @param string $scheme
   *
   * @return Domain
   */
  public function setScheme(string $scheme): Domain
  {
    $this->scheme = $scheme;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsMaster(): bool
  {
    return $this->isMaster;
  }

  /**
   * @param bool $isMaster
   *
   * @return Domain
   */
  public function setIsMaster(bool $isMaster): Domain
  {
    $this->isMaster = $isMaster;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsEnabled(): bool
  {
    return $this->isEnabled;
  }

  /**
   * @param bool $isEnabled
   *
   * @return Domain
   */
  public function setIsEnabled(bool $isEnabled): Domain
  {
    $this->isEnabled = $isEnabled;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsVirtual(): bool
  {
    return $this->isVirtual;
  }

  /**
   * @param bool $isVirtual
   *
   * @return $this
   */
  public function setIsVirtual(bool $isVirtual): Domain
  {
    $this->isVirtual = $isVirtual;
    return $this;
  }

  /**
   * @return bool
   */
  public function getIsRedirect(): bool
  {
    return $this->getRedirectUrl() !== null;
  }

  /**
   * @return string|null
   */
  public function getRedirectUrl(): ?string
  {
    return $this->redirectUrl;
  }

  /**
   * @param string|null $redirectUrl
   *
   * @return Domain
   */
  public function setRedirectUrl(?string $redirectUrl): Domain
  {
    $this->redirectUrl = $redirectUrl;
    return $this;
  }

  /**
   * @return bool
   */
  public function getRedirectWithUri(): bool
  {
    return $this->redirectWithUri;
  }

  /**
   * @param bool $withUri
   *
   * @return Domain
   */
  public function setRedirectWithUri(bool $withUri): Domain
  {
    $this->redirectWithUri = $withUri;
    return $this;
  }

  /**
   * @return bool
   */
  public function getOnePage(): bool
  {
    return $this->onePage;
  }

  /**
   * @param bool $onePage
   *
   * @return $this
   */
  public function setOnePage(bool $onePage): Domain
  {
    $this->onePage = $onePage;
    return $this;
  }

  /**
   * @return string|null
   */
  public function getLanguage(): ?string
  {
    return $this->language;
  }

  /**
   * @param string|null $language
   *
   * @return Domain
   */
  public function setLanguage(?string $language): Domain
  {
    $this->language = $language;
    return $this;
  }

  /**
   * @return int|null
   */
  public function getPosition(): ?int
  {
    return $this->position;
  }

  /**
   * @param int|null $position
   *
   * @return Domain
   */
  public function setPosition(?int $position): Domain
  {
    $this->position = $position;
    return $this;
  }

  /**
   * @var string|null
   */
  protected ?string $requestLanguage = null;

  /**
   * @return string|null
   */
  public function getCurrentLanguage(): ?string
  {
    return $this->language ?? $this->requestLanguage;
  }

  /**
   * @return string|null
   */
  public function getRequestLanguage(): ?string
  {
    return $this->requestLanguage;
  }

  /**
   * @param string|null $requestLanguage
   *
   * @return $this
   */
  public function setRequestLanguage(string $requestLanguage = null): Domain
  {
    $this->requestLanguage = $requestLanguage;
    return $this;
  }

  /**
   * getDomainsTranslate
   *
   * @return array
   */
  public function getDomainsTranslate(): array
  {
    $domainsByTranslate = array();
    /** @var DomainInterface $virtual */
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
   * getDomainTranslateByLanguage
   *
   * @param ?string $language
   * @return ?DomainInterface
   */
  public function getDomainTranslateByLanguage(?string $language = null): ?DomainInterface
  {
    return $language ? AustralTools::getValueByKey($this->getDomainsTranslate(), $language, null) : null;
  }
}