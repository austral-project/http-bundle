# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

- `tests/DependencyInjection/ContainerTest.php`: compiles the container, creates every service, and checks the public alias and the tags of the listener.

### Changed

- Sources moved to `src/` (PSR-4 `Austral\HttpBundle\` → `src/`).
- **BREAKING** `HttpHandler` receives its services through the constructor: `EventDispatcherInterface`, `TranslatorInterface`, `RouterInterface`, the kernel environment (`string $environment`, read by `isDevEnv()`) and `?Debug`. It no longer receives the service container. The request is set with `setRequestStack()`.
- **BREAKING** `HttpController` no longer uses `ContainerAwareTrait` (removed in Symfony 7) nor the service container: the constructor receives the router and Twig.
- **BREAKING** `HttpEventSubscriber` receives `ConfigurationInterface` and `Debug` through its constructor. It no longer receives the service container nor `DomainsManagement`, and no longer initialises the domains when it is built.
- **BREAKING** Services are defined explicitly (no autowiring, no autoconfiguration). Service ids named after a class (`Austral\HttpBundle\…`) are private: use the public `austral.http.config` alias to get the configuration from the container. `austral.http.request` is a private alias, to reference in a service definition (`'@austral.http.request'`). Autowiring a class of the bundle in an application still works.
- **BREAKING** The XML namespace of the bundle configuration is `https://austral.dev/schema/dic/austral_http` (it was `https://austral.app/schema/dic/austral_http`).
- **BREAKING** `HttpHandler::setRedirectStatus()` takes an `int` (it took a `string`); `getRedirectStatus()` returns an `int`.
- http-bundle no longer depends on security-bundle, form-bundle or admin-bundle.
- **BREAKING** `compression_gzip.other` is replaced by `compression_gzip.default`, which applies to every request. The `admin` and `website` keys are read by admin-bundle and website-bundle.
- The dependencies used by the code are declared in `composer.json` (contracts, tools, entity, Symfony components and contracts, Twig, `ext-zlib`).

### Fixed

- `compression_gzip` is read from the configuration. The compression was always enabled.

### Removed

- **BREAKING** The domain management moved to the new **austral/domain-bundle**, without alias in http-bundle:
  - the `Domain` entity, `DomainInterface`, `FilterByDomainTrait` and the `App\Entity\Austral\HttpBundle\Domain` skeleton;
  - the `DomainFilter` annotation (now the `Austral\DomainBundle\Attribute\DomainFilter` attribute) and `DomainFilterMapping`;
  - `DomainsManagement`, `DomainEntityManager` and `DomainRepository`;
  - the `austral:domain:migrate` command (`DomainAttachementMigrateCommand`);
  - the `resolve_target_entities` compiler pass of `DomainInterface`;
  - the domain administration screen (`Admin\DomainAdmin`).
- **BREAKING** Methods removed from `HttpHandler` and `HttpHandlerInterface`: `setContainer()`, `getRequest()` / `setRequest()` (use `setRequestStack()`), `setDispatcher()`, `getEntityManager()`, `getFormFactory()` / `setFormFactory()`, `getUser()`, `isGranted()`, `getUserTabId()` / `setUserTabId()`.
- **BREAKING** Methods removed from `HttpController`: `setTwig()`, `setTokenStorage()`, `getUser()`, `forward()`, `getRequest()`, `getSession()`, `getTranslate()`, `createNotFoundException()`, `createAccessDeniedException()`.
- **BREAKING** `HttpEventSubscriber::setTwig()`.
- **BREAKING** `HttpController::setContainer()`, `has()` and `get()`, and `HttpControllerInterface::setContainer()`: a controller receives the services that it uses through its constructor.
- **BREAKING** The `austral.http.listener` alias: the listener is called by the event dispatcher, not fetched from the container. Its service id is `Austral\HttpBundle\Listener\HttpListener`.

## [3.1.2] - 2022-12-16

- Fix deprecated functions for security compatibility from symfony 5.4
- Remove deprecated in compiler doctrine
- Fix Error handler if token is null
- Fix not change domainId if object has one
- Fix init filterDomainId by master default
- Add logo for domain entity
- Add choice to redirect domain with uri

## [3.1.1] - 2022-10-05

- Add a master domain if the domain is virtual
- Add check is redirect domain

## [3.1.0] - 2022-09-17

- Updated bundles version to 3.1 for use the new Entities Interfaces
- Added Domain entity
- Updated HttpListener
- Adding HttpRequest service
- Adding DomainManagement service

## [3.0.0] - 2021-02-10

- Created new repository and new branch in to Github
- Changed the project License to the MIT
- Writing and publish documentation
- Add to Packagist Service for installation with composer

## 2.x and 1.x

These versions were not open source and have no public documentation.

[Unreleased]: https://github.com/austral-project/http-bundle/compare/v3.1.4...4.x
[3.1.2]: https://github.com/austral-project/http-bundle/compare/v3.1.1...v3.1.2
[3.1.1]: https://github.com/austral-project/http-bundle/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/austral-project/http-bundle/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/austral-project/http-bundle/releases/tag/v3.0.0
