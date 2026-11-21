# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

### Changed

- Sources moved to `src/` (PSR-4 `Austral\HttpBundle\` → `src/`).
- **BREAKING** `HttpHandler` receives its services through the constructor (`ContainerInterface`, `EventDispatcherInterface`, `TranslatorInterface`, `RouterInterface`, `Debug`, autowired). The request is set with `setRequestStack()`.
- **BREAKING** `HttpController` no longer uses `ContainerAwareTrait` (removed in Symfony 7): the constructor receives the container, the router and Twig.
- **BREAKING** `HttpEventSubscriber` no longer receives `DomainsManagement` and no longer initialises the domains when it is built.
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
