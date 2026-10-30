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
- Tests for the traits that run certbot — `CertbotTrait`, `CertbotCertificatesTrait`,
  `CertbotCertOnlyTrait`, `CertbotDeleteTrait`, `CertbotRenewTrait`, `CertbotRevokeTrait` — and for
  the command lines `CertbotOptions` and `CertbotCertOnlyOptions` write. A recording double replaces
  `system()` and `proc()`, so they run without certbot and without sudo.
- `composer coverage` and `composer coverage:md`, as in the other `oihana/php-*` libraries: an
  HTML report and a Markdown summary in `build/coverage/`. The library is covered in full.

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
- The traits' documentation tells what each command does: the delete, revoke and certificates
  traits had kept the words of another command, `CertbotCertOnlyTrait` announced a dry run by
  default, and two traits began with leftover shell lines. Members are in order.
- `$silent` only hides the output of the command. The traits also asked for `--quiet`, which never
  reached certbot: none of the options classes but `CertbotCertOnlyOptions` holds it. The request is
  gone, and `certbotCertificates()` documents why it must never send `--quiet`: the list is read
  from the very output it would empty.
- Dead code is gone: an empty-string check in `certbotDelete()` and `certbotRevoke()`, a redundant
  check for an empty list in `certbotCertificate()` and one for a missing expiry date in
  `certbotCertificateIsValid()`. A certificate's domains are matched as a list, whether the
  certificate holds a list or a single string. No behaviour changes.

### Fixed

- **`CertbotDeleteTrait` revoked the certificate it was asked to delete.** It ran
  `certbot revoke` where it meant `certbot delete`: asked to remove a certificate from the
  machine, it revoked it at the certificate authority — an operation of its own, and one that
  cannot be undone. It now runs `certbot delete`.
- **`certbotRenew()` renewed every certificate when asked for one.** It tested an undefined
  variable instead of the name it was given, so `--cert-name` never reached certbot, which then
  renewed every certificate close to its expiry. The name is now passed. The name is the
  certificate's name, as `certbot certificates` lists it — which is not always its first domain.
- `certbotRenew( …, assertable: true )` refused an unknown certificate with a garbled message
  (“The domains … don' exist.”); it now says “The certificate … does not exist.”

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


