# Oihana PHP Certbot library - Change Log

All notable changes to this project will be documented in this file.

This project adheres to [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)  
and follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added

- `oihana\certbot\helpers\certificateKeyType()` reads the type of key a certificate was issued
  for, `ECDSA` or `RSA`, from the PEM text of the certificate — the first of a chain, such as
  certbot's `fullchain.pem` — through the OpenSSL extension, without running a process. The answer
  uses the words of the `Key Type:` line `parseCertbotCertificates()` reads, so the two compare as
  they are; another type of key, or a text that holds no certificate, gives `null`. Taken from an
  application built on this library, with nothing of that application left in it. The library now
  requires `ext-openssl`.
- Tests for `CertbotCertificate` (`isExpired()`, `isValid()`), `parseCertbotCertificates()`,
  `getCertbotDomains()`, the `CertbotSubCommand` and `CertbotParam` enums and `CertbotOption`.
- `composer coverage` and `composer coverage:md`, as in the other `oihana/php-*` libraries: an
  HTML report and a Markdown summary in `build/coverage/`. The traits that run certbot have no
  test yet.

### Changed

- The options classes come from `oihana\options` (formerly `oihana\commands\options`), and the
  enums from `oihana/php-enums`; `oihana/php-exceptions` and `oihana/php-signals` are required.
- Dependencies: dropped the now-unused `oihana/php-system` requirement — `php-certbot` consumes no
  `php-system` namespace directly. `minimum-stability: dev` with `prefer-stable: true` lets the
  split packages pulled in through `oihana/php-commands` resolve now that they carry stable tags.
  No public API changes.
- `composer.lock` is no longer committed, as for a library; `.gitignore` also leaves out
  `/build/`, `.phpunit.cache` and `.phpunit.result.cache`.
- `README.md`: the related packages no longer list `oihana/php-system`.

### Fixed

- **`CertbotDeleteTrait` revoked the certificate it was asked to delete.** It ran
  `certbot revoke` where it meant `certbot delete`: asked to remove a certificate from the
  machine, it revoked it at the certificate authority — an operation of its own, and one that
  cannot be undone. It now runs `certbot delete`.

---

## [1.0.1] - 2025-08-13
- Use the Options and Option classes in the oihana/commands/options package

## [1.0.0] - 2025-08-13

### Added

- Initial stable release.
- Strongly-typed options for Certbot subcommands:
  - `CertbotCertOnlyOptions`, `CertbotRenewOptions`, `CertbotRevokeOptions`,
    `CertbotDeleteOptions`, `CertbotCertificatesOptions`, and base `CertbotOptions`.
- Plugin and webroot support:
  - `CertbotWebrootOptions` plus plugin traits for manual, nginx, and webroot flows.
- Trait-based API you can mix into your services:
  - `CertbotTrait` (bundling `CertbotCertificatesTrait`, `CertbotCertOnlyTrait`,
    `CertbotRenewTrait`, `CertbotRevokeTrait`, `CertbotDeleteTrait`).
- Helpers:
  - `getCertbotDomains()` to safely build domain lists.
  - `parseCertbotCertificates()` to parse `certbot certificates` output.
- Entity:
  - `CertbotCertificate` with typed properties and `isExpired()` / `isValid()` helpers.
- Enums:
  - `CertbotSubCommand` and `CertbotParam`.
- Execution features:
  - Optional verbose and silent modes, dry-run support, and automatic sudo execution.
- Tooling:
  - PHPUnit configuration and Composer scripts.

---


