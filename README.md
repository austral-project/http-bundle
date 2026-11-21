# Austral Http

[![Version](https://img.shields.io/packagist/v/austral/http-bundle?label=version)](https://packagist.org/packages/austral/http-bundle)
[![CI](https://github.com/austral-project/http-bundle/actions/workflows/ci.yml/badge.svg?branch=4.x)](https://github.com/austral-project/http-bundle/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/packagist/dependency-v/austral/http-bundle/php?version=4.x-dev)](https://packagist.org/packages/austral/http-bundle)
[![Symfony](https://img.shields.io/badge/symfony-7.4-black)](https://symfony.com/releases/7.4)
[![License](https://img.shields.io/github/license/austral-project/http-bundle)](LICENSE)

Base of the HTTP layer of Austral: kernel listener, `HttpEvent` dispatched on each step of a request (initialise, request, controller, response, exception, finish), base classes for controllers, handlers and subscribers, current language and environment, gzip / deflate compression of the responses.

> This bundle is part of [Austral](https://github.com/austral-project), a modular back office and CMS for Symfony.

## Requirements

- PHP 8.4 or higher
- Symfony 7.4

## Installation

```bash
composer require austral/http-bundle
```

If you do not use Symfony Flex, register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Austral\HttpBundle\AustralHttpBundle::class => ['all' => true],
];
```

## Configuration

```yaml
# config/packages/austral_http.yaml
austral_http:
    protocol: https            # default: https
    default_language: en       # default: en
    languages: [fr, en]
    env:
        current: prod          # default: prod
        list: [prod]
    compression_gzip:
        default: true      # gzip / deflate compression of the responses
        # admin: true      # read by admin-bundle for the administration requests
        # website: true    # read by website-bundle for the website requests
```

`compression_gzip.default` applies to every request. admin-bundle and website-bundle override it for their own requests with the `admin` and `website` keys, and fall back to `default` when the key is not set.

The domains (multi-site) are managed by [austral/domain-bundle](https://github.com/austral-project/domain-bundle), which http-bundle does not depend on.

## Usage

A route opts in to the Austral HTTP events with the `_austral_http_event` attribute, set to the class of the event to dispatch (an `HttpEventInterface`). The upper bundles (admin, website) subscribe to these events through `HttpEventSubscriber`.

## Documentation

The full documentation is available at https://austral.dev.

## Versions

| Bundle | Symfony | PHP | Branch |
|--------|---------|-----|---------|
| 4.x    | 7.4     | ≥ 8.4 | `4.x` |
| 3.1    | 5.4 / 6.4 | ≥ 8.0 | `3.1` (maintenance) |

See the [CHANGELOG](CHANGELOG.md) and, to upgrade from 3.1, [UPGRADE-4.0](UPGRADE-4.0.md).

## Contributing

Contributions are welcome: see the [contributing guide](https://github.com/austral-project/.github/blob/master/CONTRIBUTING.md). To report a security issue, follow the [security policy](https://github.com/austral-project/.github/blob/master/SECURITY.md).

## License

MIT, see [LICENSE](LICENSE).

## Credits

Created by [Matthieu Beurel](https://www.mbeurel.com). Sponsored by [Yipikai Studio](https://yipikai.studio).
