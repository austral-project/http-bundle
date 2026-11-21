# Upgrade from 3.1 to 4.0

## Requirements

- PHP 8.4 minimum
- Symfony 7.4
- Doctrine ORM 3

## Update the dependencies

```diff
- "austral/http-bundle": "^3.1"
+ "austral/http-bundle": "^4.0"
```

All `austral/*` packages must be upgraded to `^4.0` at the same time.

## Breaking changes

### `compression_gzip`: `other` replaced by `default`

`compression_gzip.default` applies to every request. admin-bundle and website-bundle override it for their own requests with the `admin` and `website` keys, and fall back to `default` when their key is not set. The `other` key is no longer used.

```diff
 # config/packages/austral_http.yaml
 austral_http:
     compression_gzip:
         admin: true
         website: true
-        other: true
+        default: true
```

### Domain management moved to `austral/domain-bundle`

http-bundle no longer handles domains. Everything related to domains is in the new **austral/domain-bundle**, and http-bundle does not depend on it. A project that uses domains must install it:

```bash
composer require austral/domain-bundle:^4.0
```

and register `Austral\DomainBundle\AustralDomainBundle` in `config/bundles.php`.

| 3.1 (`Austral\HttpBundle\…`) | 4.0 (`Austral\DomainBundle\…`) |
|-----|-----|
| `Entity\Domain` | `Entity\Domain` |
| `Entity\Interfaces\DomainInterface` | `Entity\Interfaces\DomainInterface` |
| `Entity\Traits\FilterByDomainTrait` | `Entity\Traits\FilterByDomainTrait` |
| `Annotation\DomainFilter` (annotation) | `Attribute\DomainFilter` (PHP attribute) |
| `Mapping\DomainFilterMapping` | `Mapping\DomainFilterMapping` |
| `Services\DomainsManagement` | `Services\DomainsManagement` |
| `EntityManager\DomainEntityManager` | `EntityManager\DomainEntityManager` |
| `Repository\DomainRepository` | `Repository\DomainRepository` |
| `Command\DomainAttachementMigrateCommand` | `Command\DomainAttachementMigrateCommand` |
| `Admin\DomainAdmin` | `Bridge\Admin\Admin\DomainAdmin` (loaded only when admin-bundle is installed) |

The `austral:domain:migrate` command keeps its name.

#### Domain entity of the application

The entity generated in the application changes namespace. The table keeps its name (`austral_http_domain`): **no data migration is needed**.

```diff
- namespace App\Entity\Austral\HttpBundle;
+ namespace App\Entity\Austral\DomainBundle;

- use Austral\HttpBundle\Entity\Domain as BaseDomain;
+ use Austral\DomainBundle\Entity\Domain as BaseDomain;
```

Move `src/Entity/Austral/HttpBundle/Domain.php` to `src/Entity/Austral/DomainBundle/Domain.php`, and replace the `@ORM` annotations with attributes (see the skeleton of domain-bundle).

#### Updating a project

```bash
grep -rl 'Austral\\HttpBundle\\' src/ config/ | xargs perl -pi -e 's/Austral\\HttpBundle\\Annotation\\DomainFilter/Austral\\DomainBundle\\Attribute\\DomainFilter/g; s/Austral\\HttpBundle\\(Entity\\Domain|Entity\\Interfaces\\DomainInterface|Entity\\Traits\\FilterByDomainTrait|Mapping\\DomainFilterMapping|Services\\DomainsManagement|EntityManager\\DomainEntityManager|Repository\\DomainRepository|Command\\DomainAttachementMigrateCommand)/Austral\\DomainBundle\\$1/g; s/Austral\\HttpBundle\\Admin\\DomainAdmin/Austral\\DomainBundle\\Bridge\\Admin\\Admin\\DomainAdmin/g; s/App\\Entity\\Austral\\HttpBundle\\Domain/App\\Entity\\Austral\\DomainBundle\\Domain/g'
```

The `@DomainFilter(...)` annotations themselves must be rewritten as `#[DomainFilter(...)]` attributes.

### Base classes: `HttpHandler`, `HttpController` and `HttpEventSubscriber`

These abstract classes are extended by the handlers, controllers and subscribers of the upper bundles (admin, website) and of the applications. Their constructors and their methods change.

#### Constructors

| Class | 3.1 | 4.0 |
|-------|-----|-----|
| `HttpHandler` | `RequestStack, EventDispatcherInterface, TokenStorageInterface, ?Debug` | `ContainerInterface, EventDispatcherInterface, TranslatorInterface, RouterInterface, ?Debug` (autowired) |
| `HttpController` | `?Environment $twig = null`, plus `ContainerAwareTrait` | `ContainerInterface, RouterInterface, Environment` (autowired) |
| `HttpEventSubscriber` | `ContainerInterface, ConfigurationInterface, DomainsManagement, Debug` | `ContainerInterface, ConfigurationInterface, Debug` (autowired) |

A subclass that declares its own constructor must call `parent::__construct()` with the new arguments. The request of a handler is now set with `setRequestStack(RequestStack $requestStack)`.

`HttpEventSubscriber` no longer initialises the domains. A subscriber that needs them injects `Austral\DomainBundle\Services\DomainsManagement` itself (see "Domain management moved to `austral/domain-bundle`").

#### Removed methods

| Class | Removed | Replacement |
|-------|---------|-------------|
| `HttpHandler`, `HttpHandlerInterface` | `getRequest()`, `setRequest()` | `setRequestStack()` |
| | `setContainer()`, `setDispatcher()` | constructor |
| | `getDomainsManagement()`, `setDomainsManagement()` | inject `DomainsManagement` (domain-bundle) |
| | `getUser()`, `isGranted()` | inject `TokenStorageInterface` / `AuthorizationCheckerInterface` (Symfony Security) |
| | `getUserTabId()`, `setUserTabId()`, `getFormFactory()`, `setFormFactory()`, `getEntityManager()` | none in http-bundle: the handler that needs them declares them |
| `HttpController` | `setTwig()`, `setTokenStorage()` | constructor |
| | `getUser()`, `getRequest()`, `getSession()`, `getTranslate()`, `forward()`, `createNotFoundException()`, `createAccessDeniedException()` | none in http-bundle: inject the Symfony service you need |
| `HttpEventSubscriber` | `setTwig()` | none |

#### Changed signatures

```diff
- public function setRedirectStatus(string $redirectStatus): HttpHandler
+ public function setRedirectStatus(int $redirectStatus): self
- public function getRedirectStatus(): ?int
+ public function getRedirectStatus(): int
```
