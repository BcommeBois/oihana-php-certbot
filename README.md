# Oihana PHP – Certbot

[![Oihana PHP Certbot](https://raw.githubusercontent.com/BcommeBois/oihana-php-certbot/main/assets/images/oihana-php-certbot-logo-inline-512x160.png)](https://github.com/BcommeBois/oihana-php-certbot)

A PHP toolkit to create, inspect, renew, revoke, and delete [Let’s Encrypt](https://letsencrypt.org/) certificates by driving the [Certbot](https://certbot.eff.org/) CLI. It provides typed option objects, convenient helpers, and traits you can mix into your own services.

Built on top of the [Oihana PHP Commands](https://github.com/BcommeBois/oihana-php-commands/) library.

[![Latest Version](https://img.shields.io/packagist/v/oihana/php-certbot.svg?style=flat-square)](https://packagist.org/packages/oihana/php-certbot)
[![Total Downloads](https://img.shields.io/packagist/dt/oihana/php-certbot.svg?style=flat-square)](https://packagist.org/packages/oihana/php-certbot)
[![License](https://img.shields.io/packagist/l/oihana/php-certbot.svg?style=flat-square)](LICENSE)

## ✨ Why this library?

- Strongly-typed options for Certbot subcommands (certonly, certificates, renew, revoke, delete)
- Simple trait-based API you can add to your existing classes
- Helpers to parse `certbot certificates` output and build domains lists safely
- Sensible defaults, optional verbose and dry-run modes, and automatic sudo execution

### Requirements

- PHP 8.4+
- A Unix-like environment where `certbot` is installed and available in PATH
- Sufficient privileges to run Certbot (commands are executed with sudo)

## 📦 Installation

Install via Composer:

```bash
composer require oihana/php-certbot
```

## 🚀 Quick start

Mix the provided traits into your own service. The `CommandTrait` brings process execution utilities; `CertbotTrait` bundles all Certbot subcommand traits at once.

```php
<?php

use oihana\commands\traits\CommandTrait;
use oihana\certbot\traits\CertbotTrait;

final class CertbotService
{
    use CommandTrait;
    use CertbotTrait;

    public function __construct()
    {
        // Optional: set sensible defaults for future calls
        $this->initializeCertbotOptions([
            'certbot' => [
                'certonly' => [
                    'email'          => 'admin@example.com',
                    'agreeTos'       => true,
                    'nonInteractive' => true,
                ],
            ],
        ]);
    }
}
```

Use it in your application:

```php
$svc = new CertbotService();

// 1) Obtain a certificate using webroot
$svc->certbotCertOnly
([
    'webrootPath' => '/var/www/example',
    'domains'     => ['example.com', 'www.example.com'],
    'rsaKeySize'  => 4096,
], verbose: true);

// 2) List all managed certificates
$certs = $svc->certbotCertificates();

// 3) Check if a certificate exists and is valid
$exists = $svc->certbotCertificateExists('example.com');
$isValid = $svc->certbotCertificateIsValid('example.com');

// 4) Renew certificates (optionally target a single cert-name)
$svc->certbotRenew('example.com');

// 5) Revoke or delete
$svc->certbotRevoke('example.com', deleteAfter: true);
$svc->certbotDelete('example.com');
```

## ℹ️ API overview

All methods below are available when you include `oihana\certbot\traits\CertbotTrait` (or pick the specific sub-traits you need).

- Certificates inspection
  - `certbotCertificates(array|CertbotCertificatesOptions $options = null, bool $verbose = false, bool $silent = false, bool $dryRun = false): array<CertbotCertificate>`
  - `certbotCertificate(null|string|array $needs, array|CertbotCertificatesOptions $options = null, ...): ?CertbotCertificate`
  - `certbotCertificateExists(null|string|array $needs, ...): bool`
  - `certbotCertificateIsValid(null|string|array|CertbotCertificate $needs, ...): bool`

- Obtain certificate
  - `initializeCertbotCertOnlyOptions(array $init): static`
  - `certbotCertOnly(array|CertbotCertOnlyOptions $options = null, bool $verbose = false, bool $silent = false, bool $dryRun = false): int`

- Renew
  - `initializeCertbotRenewOptions(array $init): static`
  - `certbotRenew(null|string|array $domains = null, array|CertbotRenewOptions $options = null, bool $verbose = false, bool $silent = false, bool $assertable = false, bool $dryRun = false): int`

- Revoke
  - `initializeCertbotRevokeOptions(array $init): static`
  - `certbotRevoke(null|string|array $domains = null, ?bool $deleteAfter = null, array|CertbotRevokeOptions $options = null, bool $verbose = false, bool $silent = false, bool $dryRun = false): int`

- Delete
  - `initializeCertbotDeleteOptions(array $init): static`
  - `certbotDelete(null|string|array $domains = null, array|CertbotDeleteOptions $options = null, bool $verbose = false, bool $silent = false, bool $dryRun = false): int`

### Options

Each subcommand has a dedicated options class (`CertbotCertOnlyOptions`, `CertbotRenewOptions`, `CertbotRevokeOptions`, `CertbotDeleteOptions`, `CertbotCertificatesOptions`) extending `CertbotOptions`. You can either:

- Pass an associative array (flat or nested under `['certbot' => ['<subcommand>' => [...]] ]`).
- Instantiate the options object directly and pass it.

Common options include:

- `domains` (array|string): domains to include in the certificate
- `certName` (string): the Certbot internal name
- `dryRun` (bool): test mode for `certonly`/`renew`
- `nonInteractive`, `agreeTos`, `email`, `verbose`, etc.

Plugins and webroot:

- Webroot support via `CertbotWebrootOptions` (`webrootPath`, `webrootMap`)
- Additional plugin traits for manual, nginx, and webroot

### Return types

- Commands return process exit codes (`int`), where `0` indicates success.
- Inspection commands return arrays of `oihana\certbot\entities\CertbotCertificate` with parsed fields: `name`, `domains`, `expires`, `validDays`, paths, etc.

### Helpers

- `oihana\certbot\helpers\getCertbotDomains(string $domain, ?string $subdomain, bool $throw = true): ?array` – builds a safe domain list (e.g. `example.com` + `www.example.com`).
- `oihana\certbot\helpers\parseCertbotCertificates(string $source, ?string $timezone = 'Europe/Paris', ?string $dateFormat = 'Y-m-d\\TH:i:s'): array` – parses the output of `certbot certificates` into `CertbotCertificate` instances.

### CLI behavior

- Commands are executed with sudo.
- Use the `$verbose`, `$silent`, and `$dryRun` flags on methods to adjust behavior.
- Ensure `certbot` is installed and your environment has the necessary permissions.

## ✅ Running Unit Tests

```bash
composer test
```

## 🤝 Contributing

Bug reports and PRs are welcome. Please try to include tests when fixing a bug or adding a feature. Make sure to run the test suite before submitting.

## 🧾 License

This project is licensed under the [Mozilla Public License 2.0 (MPL‑2.0)](https://www.mozilla.org/en-US/MPL/2.0/).

## 👤 About the author
- Author : Marc ALCARAZ (aka eKameleon)
- Mail : [marc@ooop.fr](mailto:marc@ooop.fr)
- Website : http://www.ooop.fr

## 🔗 Related Packages

- `oihana/php-core` – core helpers and utilities used by this library: `https://github.com/BcommeBois/oihana-php-core`
- `oihana/php-reflect` – reflection and hydration utilities: `https://github.com/BcommeBois/oihana-php-reflect`
- `oihana/php-files` – filesystem helpers: `https://github.com/BcommeBois/oihana-php-files`
- `oihana/php-system` – common helpers of the Oihana PHP framework: `https://github.com/BcommeBois/oihana-php-system`
- `oihana/php-schema` – object‑oriented implementation of the Schema.org vocabulary: `https://github.com/BcommeBois/oihana-php-schema`
- `oihana/php-standards` – constants and helpers based on major international standards: `https://github.com/BcommeBois/oihana-php-standards`
- `oihana/php-commands` – scriptable command-line tooling for servers and web applications: `https://github.com/BcommeBois/oihana-php-commands`