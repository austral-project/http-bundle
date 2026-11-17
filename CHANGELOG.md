# Changelog

All notable changes to this bundle are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html). All Austral bundles share the same major and minor version.

## [Unreleased]

### Added

### Changed

### Fixed

### Removed

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
